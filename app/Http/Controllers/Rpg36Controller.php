<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustGroup;
use App\Models\SaleCompanyCommission;
use App\Models\SaleSource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use ZipArchive;

class Rpg36Controller extends Controller
{
    public function rpg36(Request $request)
    {
        $payload = $this->prepareRpg36Data($request);

        return view('rpg36.index')
            ->with('sources', $payload['sources'])
            ->with('years', $payload['years'])
            ->with('request', $request)
            ->with('datas', $payload['datas'])
            ->with('sums', $payload['sums'])
            ->with('firstDay', $payload['firstDay'])
            ->with('lastDay', $payload['lastDay']);
    }

    public function exportXlsx(Request $request)
    {
        $payload = $this->prepareRpg36Data($request);
        $fileName = sprintf(
            '員工佣金抽成_%s_%s.xlsx',
            $payload['firstDay']->format('Ymd'),
            $payload['lastDay']->format('Ymd')
        );

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $rows = [];
        if ($request->after_date || $request->before_date) {
            $start = $request->after_date ?? $payload['firstDay']->format('Y-m-d');
            $end = $request->before_date ?? $payload['lastDay']->format('Y-m-d');
            $rows[] = ['查詢區間', $start . ' ~ ' . $end];
        }

        $rows[] = ['來源名稱', '人員/廠商', '日期', '客戶名稱', '寶貝名稱', '方案', '方案價格'];

        foreach ($payload['datas'] as $typeData) {
            foreach ($typeData['companys'] as $companyData) {
                foreach ($companyData['items'] as $item) {
                    $rows[] = [
                        $typeData['name'],
                        $companyData['name'],
                        $item->sale_date,
                        $item->name,
                        $item->pet_name,
                        $item->plan_name,
                        $item->plan_price,
                    ];
                }
            }
        }

        $rows[] = [];
        $rows[] = [
            '總單數',
            $payload['sums']['count'],
            '',
            '',
            '',
            '方案總額',
            $payload['sums']['plan_price'],
        ];

        $worksheetXml = $this->buildWorksheetXml($rows);
        $tempFile = tempnam(sys_get_temp_dir(), 'rpg36_') . '.xlsx';
        $zip = new ZipArchive();
        $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', $this->buildContentTypesXml());
        $zip->addFromString('_rels/.rels', $this->buildRelsXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->buildWorkbookRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->buildWorkbookXml('員工佣金抽成'));
        $zip->addFromString('xl/worksheets/sheet1.xml', $worksheetXml);
        $zip->addFromString('xl/styles.xml', $this->buildStylesXml());
        $zip->close();

        return response()->download($tempFile, $fileName, $headers)->deleteFileAfterSend(true);
    }

    protected function prepareRpg36Data(Request $request): array
    {
        $years = range(Carbon::now()->year, 2022);

        if (!$request->after_date && !$request->before_date) {
            $search_year = $request->year ?? Carbon::now()->year;
            $search_month = $request->month ?? Carbon::now()->month;
            $firstDay = Carbon::createFromDate($search_year, $search_month)->firstOfMonth();
            $lastDay = Carbon::createFromDate($search_year, $search_month)->lastOfMonth();
        } else {
            $firstDay = Carbon::parse($request->after_date ?? Carbon::now()->startOfMonth());
            $lastDay = Carbon::parse($request->before_date ?? Carbon::now()->endOfMonth());
        }

        $sources = SaleSource::whereIn('code', ['H', 'B', 'dogpark', 'G', 'other', 'self'])->get();

        $sale_companys = SaleCompanyCommission::with(['company_name', 'self_name', 'user_name'])
            ->whereHas('sale', function ($query) {
                $query
                    ->where('plan_id', '!=', '3')
                    ->where('status', '=', '9');
            })
            ->where('sale_date', '>=', $firstDay)
            ->where('sale_date', '<=', $lastDay)
            ->where('type', '=', 'self')
            ->where('cooperation_price', '!=', '1');

        $source = $request->source;
        if ($source !== 'NULL' && isset($source)) {
            $sale_companys = $sale_companys->where('type', $source);
        }

        $sale_companys = $sale_companys->orderBy('type', 'desc')->get();
        $datas = [];

        foreach ($sale_companys as $sale_company) {
            $sourceName = SaleSource::where('code', $sale_company->type)->first();
            $datas[$sale_company->type]['name'] = $sourceName ? $sourceName->name : $sale_company->type;

            if ($sale_company->type == 'self') {
                $datas[$sale_company->type]['companys'][$sale_company->company_id]['name'] = $sale_company->self_name ? $sale_company->self_name->name : '未知用戶';
            } else {
                $datas[$sale_company->type]['companys'][$sale_company->company_id]['name'] = $sale_company->company_name ? $sale_company->company_name->name : '未知公司';
            }

            $datas[$sale_company->type]['companys'][$sale_company->company_id]['items'] = DB::table('sale_company_commission')
                ->join('sale_data', 'sale_data.id', '=', 'sale_company_commission.sale_id')
                ->leftJoin('plan', 'plan.id', '=', 'sale_data.plan_id')
                ->join('customer', 'customer.id', '=', 'sale_company_commission.customer_id')
                ->leftJoin('sale_source', 'sale_source.code', '=', 'sale_company_commission.type')
                ->where('sale_company_commission.type', '=', $sale_company->type)
                ->where('sale_company_commission.company_id', '=', $sale_company->company_id)
                ->where('sale_company_commission.sale_date', '>=', $firstDay)
                ->where('sale_company_commission.sale_date', '<=', $lastDay)
                ->where('sale_data.status', '=', '9')
                ->select(
                    'sale_company_commission.*',
                    'customer.*',
                    'sale_source.name as source_name',
                    'sale_company_commission.commission as commission_price',
                    'sale_data.status as status',
                    'plan.name as plan_name',
                    'sale_data.pet_name',
                    'sale_data.plan_price'
                )
                ->orderBy('sale_company_commission.sale_date', 'desc')
                ->get();

            $datas[$sale_company->type]['companys'][$sale_company->company_id]['count'] = DB::table('sale_company_commission')
                ->where('sale_company_commission.sale_date', '>=', $firstDay)
                ->where('sale_company_commission.sale_date', '<=', $lastDay)
                ->where('sale_company_commission.company_id', '=', $sale_company->company_id)
                ->where('sale_company_commission.type', '=', $sale_company->type)
                ->count();

            $datas[$sale_company->type]['companys'][$sale_company->company_id]['plan_amount'] = DB::table('sale_company_commission')
                ->where('sale_company_commission.sale_date', '>=', $firstDay)
                ->where('sale_company_commission.sale_date', '<=', $lastDay)
                ->where('sale_company_commission.company_id', '=', $sale_company->company_id)
                ->where('sale_company_commission.type', '=', $sale_company->type)
                ->sum('plan_price');

            $datas[$sale_company->type]['companys'][$sale_company->company_id]['commission_amount'] = DB::table('sale_company_commission')
                ->where('sale_company_commission.sale_date', '>=', $firstDay)
                ->where('sale_company_commission.sale_date', '<=', $lastDay)
                ->where('sale_company_commission.company_id', '=', $sale_company->company_id)
                ->where('sale_company_commission.type', '=', $sale_company->type)
                ->sum('commission');

            $datas[$sale_company->type]['count_total'] = 0;
            $datas[$sale_company->type]['plan_total'] = 0;
            $datas[$sale_company->type]['commission_total'] = 0;
        }

        foreach ($datas as $type => $data) {
            foreach ($data['companys'] as $company) {
                $datas[$type]['count_total'] += $company['count'];
                $datas[$type]['plan_total'] += $company['plan_amount'];
                $datas[$type]['commission_total'] += $company['commission_amount'];
            }
        }

        $sums['count'] = 0;
        $sums['plan_price'] = 0;
        $sums['commission'] = 0;
        foreach ($datas as $type => $data) {
            $sums['count'] += $data['count_total'];
            $sums['plan_price'] += $data['plan_total'];
            $sums['commission'] += $data['commission_total'];
        }

        return [
            'years' => $years,
            'sources' => $sources,
            'datas' => $datas,
            'sums' => $sums,
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
        ];
    }

    protected function buildWorksheetXml(array $rows): string
    {
        $sheetData = '';
        foreach ($rows as $rowIndex => $cells) {
            $rowNumber = $rowIndex + 1;
            $cellXml = '';
            foreach ($cells as $cellIndex => $value) {
                if ($value === null || $value === '') {
                    continue;
                }
                $cellRef = $this->columnLetterFromIndex($cellIndex) . $rowNumber;
                $escaped = htmlspecialchars((string) $value, ENT_QUOTES | ENT_XML1);
                $cellXml .= '<c r="' . $cellRef . '" t="inlineStr"><is><t>' . $escaped . '</t></is></c>';
            }
            $sheetData .= '<row r="' . $rowNumber . '">' . $cellXml . '</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>' . $sheetData . '</sheetData>'
            . '</worksheet>';
    }

    protected function columnLetterFromIndex(int $index): string
    {
        $index++;
        $letters = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letters = chr(65 + $mod) . $letters;
            $index = intdiv($index - 1, 26);
        }
        return $letters;
    }

    protected function buildContentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    protected function buildRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    protected function buildWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    protected function buildWorkbookXml(string $sheetName): string
    {
        $sheetName = htmlspecialchars($sheetName, ENT_QUOTES | ENT_XML1);

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>'
            . '<sheet name="' . $sheetName . '" sheetId="1" r:id="rId1"/>'
            . '</sheets>'
            . '</workbook>';
    }

    protected function buildStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="1"><font><sz val="11"/><color theme="1"/><name val="Calibri"/><family val="2"/></font></fonts>'
            . '<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }
}
