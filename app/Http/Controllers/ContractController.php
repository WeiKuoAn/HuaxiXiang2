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
                $datas = $datas->where('start_date', '>=', $start_date_start);
            }

            $start_date_end = $request->start_date_end;
            if ($start_date_end) {
                $datas = $datas->where('start_date', '<=', $start_date_end);
            }

            $end_date_start = $request->end_date_start;
            if ($end_date_start) {
                $datas = $datas->where('end_date', '>=', $end_date_start);
            }

            $end_date_end = $request->end_date_end;
            if ($end_date_end) {
                $datas = $datas->where('end_date', '<=', $end_date_end);
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
                
            $datas = $datas->orderby('start_date', 'asc')->paginate(50);

            $condition = $request->all();
        } else {
            $condition = '';
            $datas = $datas->orderby('start_date', 'asc')->paginate(50);
        }
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
}
