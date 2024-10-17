<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\CustGroup;
use App\Models\SaleSource;
use Illuminate\Support\Facades\DB;

class Rpg12Controller extends Controller
{
    public function rpg12(Request $request)
    {
        $years = range(Carbon::now()->year, 2022);
        if($request->input() == null){
            $search_year = $request->year;
            $search_month = $request->month;
            $firstDay = Carbon::createFromDate($search_year , $search_month)->firstOfMonth();
            $lastDay = Carbon::createFromDate($search_year , $search_month)->lastOfMonth();
        } else {
            $firstDay = $request->after_date;
            $lastDay = $request->before_date;
        }

        $CustGroups = CustGroup::where('id','!=',1)->get();

        $sources = SaleSource::whereIn('code',['H','B','dogpark','G','other'])->get();

        $sale_companys = DB::table('sale_company_commission')
                            ->join('sale_data','sale_data.id','=','sale_company_commission.sale_id')
                            ->join('customer','customer.id','=','sale_company_commission.company_id')
                            ->leftjoin('sale_source','sale_source.code','=','sale_company_commission.type')
                            ->where('sale_company_commission.sale_date','>=',$firstDay)
                            ->where('sale_company_commission.sale_date','<=',$lastDay)
                            ->whereIn('sale_company_commission.type',['H','B','dogpark','G','other'])
                            ->where('sale_data.plan_id','!=','3')
                            ->where('sale_data.status','=','9');
                            

        $source = $request->source;
        if ($source != "NULL") {
            if (isset($source)) {
                $sale_companys = $sale_companys->where('sale_company_commission.type', $source);
            } else {
                $sale_companys = $sale_companys;
            }
        }

        $sale_companys = $sale_companys->select('sale_company_commission.*','customer.*','sale_source.name as source_name','sale_data.status as status')
                                       ->orderby('sale_company_commission.type','desc')
                                        ->get();
                                    //    dd($sale_companys);
        $datas = [];
        $sums = [];

        foreach($sale_companys as $sale_company)
        {
            $datas[$sale_company->type]['name'] = $sale_company->source_name;
            $datas[$sale_company->type]['companys'][$sale_company->company_id]['name'] = $sale_company->name;
            $datas[$sale_company->type]['companys'][$sale_company->company_id]['items'] = DB::table('sale_company_commission')
                                                                                            ->join('sale_data','sale_data.id','=','sale_company_commission.sale_id')
                                                                                            ->leftjoin('plan','plan.id', '=' , 'sale_data.plan_id')
                                                                                            ->join('customer','customer.id','=','sale_company_commission.customer_id')
                                                                                            ->leftjoin('sale_source','sale_source.code','=','sale_company_commission.type')
                                                                                            // ->where('sale_company_commission.sale_date','=',$sale_company->sale_date)
                                                                                            ->where('sale_company_commission.type','=',$sale_company->type)
                                                                                            ->where('sale_company_commission.company_id','=',$sale_company->company_id)
                                                                                            ->where('sale_company_commission.sale_date','>=',$firstDay)
                                                                                            ->where('sale_company_commission.sale_date','<=',$lastDay)
                                                                                            ->where('sale_data.status','=','9')
                                                                                            ->select('sale_company_commission.*','customer.*','sale_source.name as source_name'
                                                                                                    ,'sale_data.status as status','plan.name as plan_name','sale_data.pet_name')
                                                                                            ->orderBy('sale_company_commission.sale_date','desc')
                                                                                            ->get();
            $datas[$sale_company->type]['companys'][$sale_company->company_id]['count'] = DB::table('sale_company_commission')
                                                                                                ->where('sale_company_commission.sale_date','>=',$firstDay)
                                                                                                ->where('sale_company_commission.sale_date','<=',$lastDay)
                                                                                                ->where('sale_company_commission.company_id','=',$sale_company->company_id)
                                                                                                ->where('sale_company_commission.type','=',$sale_company->type)->count();
            $datas[$sale_company->type]['companys'][$sale_company->company_id]['plan_amount'] = DB::table('sale_company_commission')
                                                                                                    ->where('sale_company_commission.sale_date','>=',$firstDay)
                                                                                                    ->where('sale_company_commission.sale_date','<=',$lastDay)
                                                                                                    ->where('sale_company_commission.company_id','=',$sale_company->company_id)
                                                                                                    ->where('sale_company_commission.type','=',$sale_company->type)
                                                                                                    ->sum('plan_price');

            $datas[$sale_company->type]['companys'][$sale_company->company_id]['commission_amount'] = DB::table('sale_company_commission')
                                                                                                        ->where('sale_company_commission.sale_date','>=',$firstDay)
                                                                                                        ->where('sale_company_commission.sale_date','<=',$lastDay)
                                                                                                        ->where('sale_company_commission.company_id','=',$sale_company->company_id)
                                                                                                        ->where('sale_company_commission.type','=',$sale_company->type)
                                                                                                        ->sum('commission');
            $datas[$sale_company->type]['count_total'] = 0;
            $datas[$sale_company->type]['plan_total'] = 0;
            $datas[$sale_company->type]['commission_total'] = 0;
        }
        foreach($datas as $type=>$data)
        {
            foreach($data['companys'] as $company)
            {
                $datas[$type]['count_total'] += $company['count'];
                $datas[$type]['plan_total'] += $company['plan_amount'];
                $datas[$type]['commission_total'] += $company['commission_amount'];
            }
        }

        $sums['count'] = 0;
        $sums['plan_price'] = 0;
        $sums['commission'] = 0;
        foreach($datas as $type=>$data)
        {
            $sums['count'] += $data['count_total'];
            $sums['plan_price'] += $data['plan_total'];
            $sums['commission'] += $data['commission_total'];
        }
        // dd($sums);

        // dd($datas);

        // foreach($datas as $data)
        // {
        //     dd($data);
        // }


        return view('rpg12.index')->with('sources',$sources)->with('years', $years)->with('request',$request)->with('datas',$datas)->with('sums',$sums)->with('firstDay',$firstDay)->with('lastDay',$lastDay);
    }
}
