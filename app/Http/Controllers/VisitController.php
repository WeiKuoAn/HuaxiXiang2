<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Customer;
use App\Models\Visit;
use App\Models\CustomerBank;
use Facade\FlareClient\View;
use Illuminate\Support\Facades\Auth;
use Whoops\Run;
use App\Models\CustGroup;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class VisitController extends Controller
{
    public function search_district(Request $request)
    {
        $hospital_type = Str::contains($request->company_type,'hospitals');//醫院

        if ($request->ajax()) {
            $output = "";

            $datas = Customer::where('group_id',2)->where('county', $request->county)->get();

            $districts = [];
            foreach($datas as $data)
            {
                $districts[] = $data->district;
            }
            $districts = array_unique($districts);
            
            if(isset($districts)){
                foreach ($districts as $key => $district) {
                    $output.=  '<option value="'.$district.'">'.$district.'</option>';
                  }
            }else{
                $output.=  '<option value="">請選擇...</option>';
            }
            // dd($output);
            return Response($output);
        }
    }

    public function index(Request $request,$id)
    {
        $datas = Visit::where('customer_id',$id);
        if(isset($request))
        {
            $after_date = $request->after_date;
            if($after_date){
                $datas = $datas->where('date','>=',$after_date);
            }
            $before_date = $request->before_date;
            if($before_date){
                $datas = $datas->where('date','<=',$before_date);
            }
            $comment = $request->comment;
            if($comment){
                $comment = $request->comment.'%';
                $datas = $datas->where('comment','like',$comment);
            }
        }
        $datas = $datas->paginate(50);
        $customer = Customer::where('id',$id)->first();
        return view('visit.index')->with('datas',$datas)->with('customer',$customer)->with('request',$request);
    }

    public function create(Request $request , $id)
    {
        $customer = Customer::where('id',$id)->first();
        return view('visit.create')->with('customer',$customer);
    }

    public function store(Request $request,$id)
    {
        $customer = Customer::where('id',$id)->first();
        $data = new Visit;
        $data->customer_id = $request->customer_id;
        $data->date = $request->date;
        $data->comment = $request->comment;
        $data->user_id = Auth::user()->id;
        $data->save();
        return redirect()->route('visits',$id)->with('customer',$customer);
    }

    public function show(Request $request , $cust_id ,$id)
    {
        $customer = Customer::where('id',$cust_id)->first();
        $data = Visit::where('customer_id',$cust_id)->where('id',$id)->first();
        return view('visit.edit')->with('customer',$customer)->with('data',$data);
    }

    public function update(Request $request , $cust_id ,$id)
    {
        // dd($id);
        $customer = Customer::where('id',$cust_id)->first();
        $data = Visit::where('customer_id',$cust_id)->where('id',$id)->first();
        // dd($data);
        $data->customer_id = $request->customer_id;
        $data->date = $request->date;
        $data->comment = $request->comment;
        $data->save();
        return redirect()->route('visits',$cust_id)->with('customer',$customer);
    }

    public function delete(Request $request , $cust_id ,$id)
    {
        $customer = Customer::where('id',$cust_id)->first();
        $data = Visit::where('customer_id',$cust_id)->where('id',$id)->first();
        return view('visit.del')->with('customer',$customer)->with('data',$data);
    }

    public function destroy(Request $request , $cust_id ,$id)
    {
        $customer = Customer::where('id',$cust_id)->first();
        Visit::where('customer_id',$cust_id)->where('id',$id)->delete();
        return redirect()->route('visits',$id)->with('customer',$customer);
    }

    public function hospitals(Request $request)
    {
        $datas = Customer::where('group_id',2);
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%'.$request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
            $county = $request->county;
            if ($county != "null") {
                if (isset($county)) {
                    $datas = $datas->where('county', $county);
                } else {
                    $datas = $datas;
                }
            }
            $district = $request->district;
            if ($district != "null") {
                if (isset($district)) {
                    $datas = $datas->where('district', $district);
                } else {
                    $datas = $datas;
                }
            }
        }
        $datas = $datas->orderby('name','desc')->paginate(50);
        
        $data_countys = Customer::where('group_id',2)->get();
        foreach($data_countys as $data_county)
        {
            $countys[] = $data_county->county;
        }
        $countys = array_unique($countys);
        
        if(isset($county))
        {
            $data_districts = Customer::where('group_id',2)->where('county', $county)->get();
        }else{
            $data_districts = [];
        }
        $districts = [];
        foreach($data_districts as $data_district)
        {
            $districts[] = $data_district->district;
        }
        $districts = array_unique($districts);
        // dd($districts);

        return view('visit.hospitals')->with('datas',$datas)->with('request',$request)->with('countys',$countys)->with('districts',$districts);
    }

    public function etiquettes(Request $request)//禮儀社
    {
        $datas = Customer::where('group_id',5);
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%'.$request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
        }
        $datas = $datas->paginate(50);

       
        return view('visit.etiquettes')->with('datas',$datas)->with('request',$request);
    }

    public function reproduces(Request $request)//繁殖場
    {
        $datas = Customer::where('group_id',4);
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%'.$request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
        }
        $datas = $datas->paginate(50);
        return view('visit.reproduces')->with('datas',$datas)->with('request',$request);
    }

    public function dogparks(Request $request)//狗園
    {
        $datas = Customer::where('group_id',3);
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%'.$request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
        }
        $datas = $datas->paginate(50);
        return view('visit.dogparks')->with('datas',$datas)->with('request',$request);
    }

    public function salons(Request $request)//美容院
    {
        $datas = Customer::where('group_id',6);
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%'.$request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
        }
        $datas = $datas->paginate(50);
        return view('visit.salons')->with('datas',$datas)->with('request',$request);
    }

    public function others(Request $request)
    {
        $datas = Customer::where('group_id',7);
        if ($request) {
            $name = $request->name;
            if (!empty($name)) {
                $name = '%'.$request->name . '%';
                $datas = $datas->where('name', 'like', $name);
            }
            $mobile = $request->mobile;
            if (!empty($mobile)) {
                $mobile = $request->mobile . '%';
                $datas = $datas->where('mobile', 'like', $mobile);
            }
        }
        $datas = $datas->paginate(50);
        return view('visit.others')->with('datas',$datas)->with('request',$request);
    }

    //新增公司
    public function company_create(Request $request)
    {
        $company_type = $request->headers->get('referer');
        // dd($company_type);
        
        return View('visit.company_create')->with('hint',0)->with('company_type',$company_type);
    }

    public function company_store(Request $request)
    {
        // dd($request->company_type);
        $hospital_type = Str::contains($request->company_type,'hospitals');//醫院
        $etiquette_type = Str::contains($request->company_type,'etiquettes');//禮儀社
        $reproduce_type = Str::contains($request->company_type,'reproduces');//繁殖場
        $dogpark_type = Str::contains($request->company_type,'dogparks');//狗園
        $salons_type = Str::contains($request->company_type,'salons');//美容院
        $others_type = Str::contains($request->company_type,'others');//其他業者

        $data = Customer::where('mobile',$request->mobile)->first();
        if($request->not_mobile == 1){ //未提供電話
            $customer = new Customer;
            $customer->name = $request->name;
            $customer->mobile = '未提供電話';
            $customer->county = $request->county;
            $customer->district = $request->district;
            $customer->address = $request->address;
            if(!empty($request->bank_id) || !empty($request->bank_number)){
                $customer->bank_id = $request->bank_id;
                $customer->bank_number = $request->bank_number;
            }
        }else{
            if(isset($data)){
                return view('visit.company_create')->with(['hint' => '1','company_type'=>$request->company_type]);
            }else{
                if(isset($data)){
                    return view('visit.company_create')->with(['hint' => '1','company_type'=>$request->company_type]);
                }else{
                    $customer = new Customer;
                    $customer->name = $request->name;
                    $customer->mobile = $request->mobile;
                    $customer->county = $request->county;
                    $customer->district = $request->district;
                    $customer->address = $request->address;
                    if(!empty($request->bank_id) || !empty($request->bank_number)){
                        $customer->bank_id = $request->bank_id;
                        $customer->bank_number = $request->bank_number;
                    }
                }
                
            }
        }

        if($hospital_type){
            $customer->group_id = 2;
            $customer->created_up = Auth::user()->id;
            $customer->save();
            return redirect()->route('hospitals');
        }elseif($etiquette_type){
            $customer->group_id = 5;
            $customer->created_up = Auth::user()->id;
            $customer->save();
            return redirect()->route('etiquettes');
        }elseif($reproduce_type){
            $customer->group_id = 4;
            $customer->created_up = Auth::user()->id;
            $customer->save();
            return redirect()->route('reproduces');
        }elseif($dogpark_type){
            $customer->group_id = 3;
            $customer->created_up = Auth::user()->id;
            $customer->save();
            return redirect()->route('dogparks');
        }elseif($salons_type){
            $customer->group_id = 6;
            $customer->created_up = Auth::user()->id;
            $customer->save();
            return redirect()->route('salons');
        }elseif($others_type){
            $customer->group_id = 7;
            $customer->created_up = Auth::user()->id;
            $customer->save();
            return redirect()->route('others');
        }
    }

    //編輯公司
    public function company_edit($id , Request $request)
    {
        $company_type = $request->headers->get('referer');
        $data = Customer::where('id', $id)->first();
        $groups = CustGroup::get();
        return View('visit.company_edit')->with('hint',0)->with('data',$data)->with('company_type',$company_type)->with('groups',$groups);
    }

    public function company_update($id , Request $request)
    {
        $hospital_type = Str::contains($request->company_type,'hospitals');//醫院
        $etiquette_type = Str::contains($request->company_type,'etiquettes');//禮儀社
        $reproduce_type = Str::contains($request->company_type,'reproduces');//繁殖場
        $dogpark_type = Str::contains($request->company_type,'dogparks');//狗園
        $salons_type = Str::contains($request->company_type,'salon');//美容院
        $others_type = Str::contains($request->company_type,'others');//其他業者

        $data = Customer::where('id', $id)->first();
        $data->name = $request->name;
        $data->mobile = $request->mobile;
        $data->county = $request->county;
        $data->district = $request->district;
        $data->address = $request->address;
        $data->group_id = $request->group_id;
        $data->bank_id = $request->bank_id;
        $data->bank_number = $request->bank_number;
        $data->save();

        if($hospital_type){
            return redirect()->route('hospitals');
        }elseif($etiquette_type){
            return redirect()->route('etiquettes');
        }elseif($reproduce_type){
            return redirect()->route('reproduces');
        }elseif($dogpark_type){
            return redirect()->route('dogparks');
        }elseif($salons_type){
            return redirect()->route('salons');
        }elseif($others_type){
            return redirect()->route('others');
        }
    }
}
