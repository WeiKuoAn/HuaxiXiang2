<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::now()->format("Y-m-d");
        $check_renew = $request->check_renew;
        if(!isset($check_renew)){
            $datas = Contract::whereIn('renew',[0,1]);
        }else{
            $datas = Contract::where('renew',$check_renew);
        }
                
        if ($request) {
            $start_date_start = $request->start_date_start;
            if ($start_date_start) {
                $gregorianStartDate = $this->convertROCtoGregorian($start_date_start);
                $datas = $datas->where('start_date', '>=', $gregorianStartDate);
            }

            $start_date_end = $request->start_date_end;
            if ($start_date_end) {
                $gregorianStartDateEnd = $this->convertROCtoGregorian($start_date_end);
                $datas = $datas->where('start_date', '<=', $gregorianStartDateEnd);
            }

            $end_date_start = $request->end_date_start;
            if ($end_date_start) {
                $gregorianEndDate = $this->convertROCtoGregorian($end_date_start);
                $datas = $datas->where('end_date', '>=', $gregorianEndDate);
            }

            $end_date_end = $request->end_date_end;
            if ($end_date_end) {
                $gregorianEndDateEnd = $this->convertROCtoGregorian($end_date_end);
                $datas = $datas->where('end_date', '<=', $gregorianEndDateEnd);
            }

            $cust_name = $request->cust_name;
            if ($cust_name) {
                $cust_name = $request->cust_name.'%';
                $customers = Customer::where('name', 'like' ,$cust_name)->get();
                foreach($customers as $customer) {
                    $customer_ids[] = $customer->id;
                }
                if(isset($customer_ids)){
                    $datas = $datas->whereIn('customer_id', $customer_ids);
                }else{
                    $datas = $datas;
                }
            }

            $pet_name = $request->pet_name;
            if ($pet_name) {
                $pet_name = $request->pet_name.'%';
                $datas = $datas->where('pet_name','like' ,$pet_name);
            }

            $type = $request->type;

            if ($type != "null") {
                if (isset($type)) {
                    $datas = $datas->where('type',  $type);
                }else{
                    $datas = $datas ;
                }
            }

            $colse = $request->check_close;
            if(!isset($colse) || $colse == '1')
            {
                $datas = $datas->whereNull('close_date');
            }else{
                $datas = $datas->whereNotNull('close_date');
            }
                
            $datas = $datas->orderby('end_date', 'asc')->paginate(50);

            $condition = $request->all();
        } else {
            $condition = '';
            $datas = $datas->orderby('start_date', 'asc')->paginate(50);
        }

        foreach($datas as $data)
        {
            $data->Roc_start_date = 1;
        }

        // dd($datas);

        $contract_types = ContractType::where('status','up')->get();
        return view('contract.index')->with('datas',$datas)
                                     ->with('contract_types',$contract_types)
                                     ->with('request',$request)
                                     ->with('condition',$condition);
    }

    public function create()
    {
        $contract_types = ContractType::where('status','up')->get();
        return view('contract.create')->with('contract_types',$contract_types);
    }

    public function store(Request $request)
    {
        // dd($request->renew);
        $data = new Contract;
        $data->type = $request->type;
        $data->number = $request->number;
        $data->customer_id = $request->cust_name_q;
        $data->pet_name = $request->pet_name;
        $data->mobile = $request->mobile;
        $data->year = $request->year;
        $data->price = $request->price;
        $data->start_date = $request->start_date;
        $data->end_date = $request->end_date;
        if(isset($request->renew)){
            $data->renew = $request->renew;
        }else{
            $data->renew = 0;
        }
        $data->renew_year = $request->renew_year;
        $data->user_id = Auth::user()->id;
        $data->comment = $request->comment;
        $data->save();
        return redirect()->route('contracts');
    }

    public function show($id)
    {
        $contract_types = ContractType::where('status','up')->get();
        $data = Contract::where('id',$id)->first();
        $sales = Sale::where('customer_id', $data->customer_id)->distinct('pet_name')->whereNotNull('pet_name')->get();
        return view('contract.edit')->with('data',$data)->with('contract_types',$contract_types)->with('sales',$sales);
    }

    public function update(Request $request, $id)
    {
        $data = Contract::where('id',$id)->first();
        $data->type = $request->type;
        $data->number = $request->number;
        $data->customer_id = $request->cust_name_q;
        $data->pet_name = $request->pet_name;
        $data->mobile = $request->mobile;
        $data->year = $request->year;
        $data->price = $request->price;
        $data->start_date = $request->start_date;
        $data->end_date = $request->end_date;

        if(isset($request->renew)){
            $data->renew = $request->renew;
            $data->renew_year = $request->renew_year;
        }else{
            $data->renew = 0;
            $data->renew_year = null;
        }
        $data->close_date = $request->close_date;
        $data->comment = $request->comment;
        $data->user_id = Auth::user()->id;
        $data->save();
        return redirect()->route('contracts');
    }

    public function delete($id)
    {
        $contract_types = ContractType::where('status','up')->get();
        $data = Contract::where('id',$id)->first();
        $sales = Sale::where('customer_id', $data->customer_id)->distinct('pet_name')->whereNotNull('pet_name')->get();
        return view('contract.del')->with('data',$data)->with('contract_types',$contract_types)->with('sales',$sales);
    }

    public function destroy(Request $request, $id)
    {
        Contract::where('id',$id)->delete();
        return redirect()->route('contracts');
    }

    public function export(Request $request)
    {
        $today = Carbon::now()->format("Y-m-d");
        $check_renew = $request->check_renew;
        if(!isset($check_renew)){
            $datas = Contract::whereIn('renew',[0,1]);
        }else{
            $datas = Contract::where('renew',$check_renew);
        }
                
        if ($request) {
            $start_date_start = $request->start_date_start;
            if ($start_date_start) {
                $gregorianStartDate = $this->convertROCtoGregorian($start_date_start);
                $datas = $datas->where('start_date', '>=', $gregorianStartDate);
            }

            $start_date_end = $request->start_date_end;
            if ($start_date_end) {
                $gregorianStartDateEnd = $this->convertROCtoGregorian($start_date_end);
                $datas = $datas->where('start_date', '<=', $gregorianStartDateEnd);
            }

            $end_date_start = $request->end_date_start;
            if ($end_date_start) {
                $gregorianEndDate = $this->convertROCtoGregorian($end_date_start);
                $datas = $datas->where('end_date', '>=', $gregorianEndDate);
            }

            $end_date_end = $request->end_date_end;
            if ($end_date_end) {
                $gregorianEndDateEnd = $this->convertROCtoGregorian($end_date_end);
                $datas = $datas->where('end_date', '<=', $gregorianEndDateEnd);
            }

            $cust_name = $request->cust_name;
            if ($cust_name) {
                $cust_name = $request->cust_name.'%';
                $customers = Customer::where('name', 'like' ,$cust_name)->get();
                foreach($customers as $customer) {
                    $customer_ids[] = $customer->id;
                }
                if(isset($customer_ids)){
                    $datas = $datas->whereIn('customer_id', $customer_ids);
                }else{
                    $datas = $datas;
                }
            }

            $pet_name = $request->pet_name;
            if ($pet_name) {
                $pet_name = $request->pet_name.'%';
                $datas = $datas->where('pet_name','like' ,$pet_name);
            }

            $type = $request->type;

            if ($type != "null") {
                if (isset($type)) {
                    $datas = $datas->where('type',  $type);
                }else{
                    $datas = $datas ;
                }
            }

            $colse = $request->check_close;
            if(!isset($colse) || $colse == '1')
            {
                $datas = $datas->whereNull('close_date');
            }else{
                $datas = $datas->whereNotNull('close_date');
            }
                
            $datas = $datas->orderby('end_date', 'asc')->get();

            $condition = $request->all();
        } else {
            $condition = '';
            $datas = $datas->orderby('start_date', 'asc')->get();
        }
        if(!isset($colse) || $colse == '1'){
            $fileName = '未結案-合約管理明細' . date("Y-m-d") . '.csv';
        }else{
            $fileName = '已結案-合約管理明細' . date("Y-m-d") . '.csv';
        }

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $header = array('合約起始日期', $start_date_start.'~' ,$start_date_end,'合約結束日期', $end_date_start.'~' ,$end_date_end);
        $columns = array('編號', '合約類別', '顧客名稱', '電話', '寶貝名稱', '目前簽約年份', '開始日期', '結束日期', '金額', '續約');

        $callback = function() use($datas, $columns,$header,$request) {
            
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF).chr(0xBB).chr(0xBF), 3); 
            fputcsv($file, $header);
            fputcsv($file, $columns);

            foreach ($datas as $data) {
                $row['編號'] = $data->number;
                $row['合約類別'] = $data->type_data->name;
                $row['顧客名稱'] = $data->cust_name->name;
                $row['電話'] = $data->mobile;
                $row['寶貝名稱'] = $data->pet_name;
                if($data->type == '4'){
                    $row['目前簽約年份'] = $data->year.'天';
                }else{
                    $row['目前簽約年份'] = '第'.$data->year.'年';
                }
                $row['開始日期'] = $data->getRocStartDateAttribute();
                if(!isset($request->check_close) || $request->check_close == '1'){
                    $row['結束日期'] = $data->getRocEndDateAttribute();
                }else{
                    $row['結束日期'] = $data->getRocCloseDateAttribute();
                }
                $row['金額'] = $data->price;
                if($data->renew == '1'){
                    $row['續約'] = '是（'.$data->renew_year.'年）';
                }else{
                    $row['續約'] = '';
                }
                fputcsv($file, array($row['編號'],$row['合約類別'],$row['顧客名稱'],$row['電話'],$row['寶貝名稱'],$row['目前簽約年份']
                                    ,$row['開始日期'],$row['結束日期'],$row['金額'],$row['續約']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    function convertROCtoGregorian($rocDate) {
        // Split the date into parts
        $parts = explode('/', $rocDate);
        if (count($parts) != 3) {
            return null; // Or handle the error as appropriate
        }
    
        // Convert ROC year to Gregorian by adding 1911
        $year = intval($parts[0]) + 1911;
        $month = $parts[1];
        $day = $parts[2];
    
        // Format to "YYYY-MM-DD"
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
    
}
