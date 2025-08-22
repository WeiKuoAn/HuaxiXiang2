<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puja;
use App\Models\PujaData;
use App\Models\Customer;
use Carbon\Carbon;

class Rpg33Controller extends Controller
{
    public function index(Request $request)
    {
        // 獲取所有法會，按年份分組
        $pujas = Puja::orderBy('date', 'desc')->get();
        $pujasByYear = $pujas->groupBy(function($puja) {
            return Carbon::parse($puja->date)->year;
        });

        // 處理複選法會查詢
        $customers = collect();
        $customerPets = collect(); // 儲存客戶的寶貝資料
        
        if ($request->has('puja_ids') && !empty($request->puja_ids)) {
            $puja_ids = $request->puja_ids;
            
            // 找出同時報名了所有選中法會的客戶
            $customer_ids = PujaData::whereIn('puja_id', $puja_ids)
                ->where('status', '1')
                ->select('customer_id')
                ->groupBy('customer_id')
                ->havingRaw('COUNT(DISTINCT puja_id) = ?', [count($puja_ids)])
                ->pluck('customer_id');

            // 獲取客戶詳細資料
            if ($customer_ids->count() > 0) {
                $customers = Customer::whereIn('id', $customer_ids)->get();
                
                // 獲取每個客戶的寶貝資料
                foreach ($customers as $customer) {
                    $pets = PujaData::whereIn('puja_id', $puja_ids)
                        ->where('customer_id', $customer->id)
                        ->where('status', '1')
                        ->pluck('pet_name')
                        ->unique()
                        ->filter()
                        ->values();
                    
                    $customerPets->put($customer->id, $pets);
                }
            }
        }

        return view('rpg33.index', compact('pujasByYear', 'customers', 'customerPets', 'request'));
    }

    public function export(Request $request)
    {
        // 處理複選法會查詢
        $customers = collect();
        if ($request->has('puja_ids') && !empty($request->puja_ids)) {
            $puja_ids = $request->puja_ids;
            
            // 找出同時報名了所有選中法會的客戶
            $customer_ids = PujaData::whereIn('puja_id', $puja_ids)
                ->where('status', '1')
                ->select('customer_id')
                ->groupBy('customer_id')
                ->havingRaw('COUNT(DISTINCT puja_id) = ?', [count($puja_ids)])
                ->pluck('customer_id');

            // 獲取客戶詳細資料
            if ($customer_ids->count() > 0) {
                $customers = Customer::whereIn('id', $customer_ids)->get();
            }
        }

        $fileName = '多法會客戶查詢_' . date("Y-m-d") . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('編號', '客戶姓名', '客戶電話', '寶貝名稱');
        // $columns = array('編號', '客戶姓名', '客戶電話', '客戶地址', '寶貝名稱', '報名法會數量');

        $callback = function() use($customers, $columns, $request) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF).chr(0xBB).chr(0xBF), 3); 
            fputcsv($file, $columns);

            foreach ($customers as $key => $customer) {
                // 獲取客戶的寶貝資料
                $pets = PujaData::whereIn('puja_id', $request->puja_ids)
                    ->where('customer_id', $customer->id)
                    ->where('status', '1')
                    ->pluck('pet_name')
                    ->unique()
                    ->filter()
                    ->values();
                
                $petNames = $pets->implode(', ');
                
                $row['編號'] = $key + 1;
                $row['客戶姓名'] = $customer->name;
                $row['客戶電話'] = $customer->mobile;
                // $row['客戶地址'] = $customer->address;
                $row['寶貝名稱'] = $petNames ?: '無';
                // $row['報名法會數量'] = count($request->puja_ids);
                fputcsv($file, array($row['編號'], $row['客戶姓名'], $row['客戶電話'], $row['寶貝名稱']));
                // fputcsv($file, array($row['編號'], $row['客戶姓名'], $row['客戶電話'], $row['客戶地址'], $row['寶貝名稱'], $row['報名法會數量']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
