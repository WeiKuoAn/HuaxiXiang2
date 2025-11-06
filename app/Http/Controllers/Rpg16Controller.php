<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Prom;
use App\Models\Sale_prom;
use Illuminate\Support\Facades\DB;

class Rpg16Controller extends Controller
{
    public function rpg16(Request $request)
    {
        $years = range(Carbon::now()->year,2022);
        $search_year = $request->year;

        if(!isset($search_year)){
            // $search_year = '2022';
            $search_year = Carbon::now()->year;
        }
        
        $months = [
            '01'=> [ 'month'=>'一月' , 'start_date'=>$search_year.'-01-01' , 'end_date'=>$search_year.'-01-31'],
            '02'=> [ 'month'=>'二月' , 'start_date'=>$search_year.'-02-01' , 'end_date'=>$search_year.'-02-29'],
            '03'=> [ 'month'=>'三月' , 'start_date'=>$search_year.'-03-01' , 'end_date'=>$search_year.'-03-31'],
            '04'=> [ 'month'=>'四月' , 'start_date'=>$search_year.'-04-01' , 'end_date'=>$search_year.'-04-30'],
            '05'=> [ 'month'=>'五月' , 'start_date'=>$search_year.'-05-01' , 'end_date'=>$search_year.'-05-31'],
            '06'=> [ 'month'=>'六月' , 'start_date'=>$search_year.'-06-01' , 'end_date'=>$search_year.'-06-30'],
            '07'=> [ 'month'=>'七月' , 'start_date'=>$search_year.'-07-01' , 'end_date'=>$search_year.'-07-31'],
            '08'=> [ 'month'=>'八月' , 'start_date'=>$search_year.'-08-01' , 'end_date'=>$search_year.'-08-31'],
            '09'=> [ 'month'=>'九月' , 'start_date'=>$search_year.'-09-01' , 'end_date'=>$search_year.'-09-30'],
            '10'=> [ 'month'=>'十月' , 'start_date'=>$search_year.'-10-01' , 'end_date'=>$search_year.'-10-31'],
            '11'=> [ 'month'=>'十一月' , 'start_date'=>$search_year.'-11-01' , 'end_date'=>$search_year.'-11-30'],
            '12'=> [ 'month'=>'十二月' , 'start_date'=>$search_year.'-12-01' , 'end_date'=>$search_year.'-12-31'],
        ];

        $datas = [];
        $sums = [];
        $proms = Prom::where('type','B')->where('status','up')->get();

        foreach($months as $key => $month)
        {
            $datas[$key]['month'] = $month['month'];
            $datas[$key]['start_date'] = $this->date_text($month['start_date']);
            $datas[$key]['end_date'] = $this->date_text($month['end_date']);
            $datas[$key]['proms']=[];
            $datas[$key]['total_count'] = 0;
            $datas[$key]['total_amount'] = 0;
            
            foreach($proms as $prom)
            {
                $datas[$key]['proms'][$prom->id]['id'] = $prom->id;
                $datas[$key]['proms'][$prom->id]['name'] = $prom->name;
                $datas[$key]['proms'][$prom->id]['count'] = DB::table('sale_data')
                                                                ->leftjoin('sale_prom','sale_prom.sale_id', '=' , 'sale_data.id')
                                                                ->whereNotNull('sale_prom.prom_id')
                                                                ->where('sale_data.sale_date','>=',$month['start_date'])->where('sale_data.sale_date','<=',$month['end_date'])
                                                                ->where('sale_prom.prom_id',$prom->id)
                                                                ->where('sale_data.status','9')
                                                                ->count();
                
                // 加入金額統計
                $datas[$key]['proms'][$prom->id]['amount'] = DB::table('sale_data')
                                                                ->leftjoin('sale_prom','sale_prom.sale_id', '=' , 'sale_data.id')
                                                                ->whereNotNull('sale_prom.prom_id')
                                                                ->where('sale_data.sale_date','>=',$month['start_date'])->where('sale_data.sale_date','<=',$month['end_date'])
                                                                ->where('sale_prom.prom_id',$prom->id)
                                                                ->where('sale_data.status','9')
                                                                ->sum('sale_prom.prom_total');
                
                $datas[$key]['total_count'] += $datas[$key]['proms'][$prom->id]['count'];
                $datas[$key]['total_amount'] += $datas[$key]['proms'][$prom->id]['amount'];
            }
            // dd($datas);
        }

        foreach($proms as $prom)
        {
            $sums[$prom->id] = [
                'count' => 0,
                'amount' => 0
            ];
        }
        
        foreach($datas as $data)
        {
            foreach($data['proms'] as $prom)
            {
                $sums[$prom['id']]['count'] += $prom['count'];
                $sums[$prom['id']]['amount'] += $prom['amount'];
            }
        }
        
        // 計算總計
        $total_summary = [
            'count' => 0,
            'amount' => 0
        ];
        foreach($datas as $data) {
            $total_summary['count'] += $data['total_count'];
            $total_summary['amount'] += $data['total_amount'];
        }
        // dd($sums);

        return view('rpg16.index')->with('datas',$datas)
                                  ->with('request',$request)
                                  ->with('search_year',$search_year)
                                  ->with('years',$years)
                                  ->with('sums',$sums)
                                  ->with('proms',$proms)
                                  ->with('months',$months)
                                  ->with('total_summary',$total_summary);

    }

    private function date_text($date)//民國顯示
    {
        $date_text = "";
    
        if($date){
            $month = mb_substr($date, 5,2);
            $day = mb_substr($date, 8,2);
    
            $date_text = $month.'/'.$day;
        }
        
        return $date_text;
    }

    public function detail(Request $request , $month , $prom_id)
    {
        $prom = Prom::where('id',$prom_id)->first();
        $search_year = $request->year;
        if(!isset($search_year)){
            $search_year = Carbon::now()->year;
        }
        $startOfMonth = Carbon::create($search_year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $datas = Sale::join('sale_prom','sale_prom.sale_id', '=' , 'sale_data.id')
                    ->where('sale_data.sale_date','>=',$startOfMonth->toDateString())->where('sale_data.sale_date','<=',$endOfMonth->toDateString())
                    ->where('sale_prom.prom_id',$prom_id)
                    ->whereNotNull('sale_prom.prom_id')
                    ->where('sale_prom.prom_type','B')
                    ->where('sale_data.status','9')
                    ->get();
                    // dd($datas);

        return view('rpg16.detail')->with('datas',$datas)->with('prom',$prom)->with('year',$search_year)->with('month',$month);
    }

    /**
     * 單一服務項目的年度詳細統計
     */
    public function serviceAnalysis(Request $request, $prom_id)
    {
        $prom = Prom::where('id', $prom_id)->first();
        
        if (!$prom) {
            return redirect()->route('rpg16')->with('error', '找不到該服務項目');
        }

        $years = range(Carbon::now()->year, 2022);
        $search_year = $request->year ?? Carbon::now()->year;

        $months = [
            '01'=> [ 'month'=>'一月', 'month_num'=>1, 'start_date'=>$search_year.'-01-01', 'end_date'=>$search_year.'-01-31'],
            '02'=> [ 'month'=>'二月', 'month_num'=>2, 'start_date'=>$search_year.'-02-01', 'end_date'=>$search_year.'-02-29'],
            '03'=> [ 'month'=>'三月', 'month_num'=>3, 'start_date'=>$search_year.'-03-01', 'end_date'=>$search_year.'-03-31'],
            '04'=> [ 'month'=>'四月', 'month_num'=>4, 'start_date'=>$search_year.'-04-01', 'end_date'=>$search_year.'-04-30'],
            '05'=> [ 'month'=>'五月', 'month_num'=>5, 'start_date'=>$search_year.'-05-01', 'end_date'=>$search_year.'-05-31'],
            '06'=> [ 'month'=>'六月', 'month_num'=>6, 'start_date'=>$search_year.'-06-01', 'end_date'=>$search_year.'-06-30'],
            '07'=> [ 'month'=>'七月', 'month_num'=>7, 'start_date'=>$search_year.'-07-01', 'end_date'=>$search_year.'-07-31'],
            '08'=> [ 'month'=>'八月', 'month_num'=>8, 'start_date'=>$search_year.'-08-01', 'end_date'=>$search_year.'-08-31'],
            '09'=> [ 'month'=>'九月', 'month_num'=>9, 'start_date'=>$search_year.'-09-01', 'end_date'=>$search_year.'-09-30'],
            '10'=> [ 'month'=>'十月', 'month_num'=>10, 'start_date'=>$search_year.'-10-01', 'end_date'=>$search_year.'-10-31'],
            '11'=> [ 'month'=>'十一月', 'month_num'=>11, 'start_date'=>$search_year.'-11-01', 'end_date'=>$search_year.'-11-30'],
            '12'=> [ 'month'=>'十二月', 'month_num'=>12, 'start_date'=>$search_year.'-12-01', 'end_date'=>$search_year.'-12-31'],
        ];

        $monthly_data = [];
        $total_count = 0;
        $total_amount = 0;

        foreach($months as $key => $month) {
            $count = DB::table('sale_data')
                ->leftjoin('sale_prom','sale_prom.sale_id', '=' , 'sale_data.id')
                ->whereNotNull('sale_prom.prom_id')
                ->where('sale_data.sale_date','>=',$month['start_date'])
                ->where('sale_data.sale_date','<=',$month['end_date'])
                ->where('sale_prom.prom_id',$prom->id)
                ->where('sale_data.status','9')
                ->count();

            $amount = DB::table('sale_data')
                ->leftjoin('sale_prom','sale_prom.sale_id', '=' , 'sale_data.id')
                ->whereNotNull('sale_prom.prom_id')
                ->where('sale_data.sale_date','>=',$month['start_date'])
                ->where('sale_data.sale_date','<=',$month['end_date'])
                ->where('sale_prom.prom_id',$prom->id)
                ->where('sale_data.status','9')
                ->sum('sale_prom.prom_total');

            $monthly_data[$key] = [
                'month' => $month['month'],
                'month_num' => $month['month_num'],
                'count' => $count,
                'amount' => $amount,
            ];

            $total_count += $count;
            $total_amount += $amount;
        }

        return view('rpg16.service_analysis')
            ->with('prom', $prom)
            ->with('monthly_data', $monthly_data)
            ->with('total_count', $total_count)
            ->with('total_amount', $total_amount)
            ->with('search_year', $search_year)
            ->with('years', $years)
            ->with('request', $request);
    }
}
