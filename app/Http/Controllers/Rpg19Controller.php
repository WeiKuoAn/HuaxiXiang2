<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Sale;

class Rpg19Controller extends Controller
{
    public function rpg19(Request $request)
    {
        $years = range(Carbon::now()->year,2022);

        $search_year = $request->year;
        $before_year = $search_year-1;

        if(!isset($search_year)){
            // $search_year = '2022';
            $search_year = Carbon::now()->year;
            $before_year = $search_year-1;
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

        $petCounts = []; // 用於存儲每個寶貝名稱的總數量
        foreach ($months as $key => $month) {
            $datas[$key]['month'] = $month['month'];
            $datas[$key]['start_date'] = $this->date_text($month['start_date']);
            $datas[$key]['end_date'] = $this->date_text($month['end_date']);
            $datas[$key]['pets'] = []; // 初始化 pets 陣列
        
            $sale_datas = Sale::where('status', '9')
                              ->where('sale_date', '>=', $month['start_date'])
                              ->where('sale_date', '<=', $month['end_date'])
                              ->whereIn('plan_id', [1, 2, 3])
                              ->whereIn('pay_id', ['A', 'C', 'E'])
                              ->get();
        
            foreach ($sale_datas as $sale_data) {
                $petName = $sale_data->pet_name;
                $datas[$key]['pets']['name'][] = $petName;
        
                // 計算每個寶貝名稱的數量
                if (!isset($petCounts[$petName])) {
                    $petCounts[$petName] = 0;
                }
                $petCounts[$petName]++;
            }
        
            // 計算本月每個寶貝名稱的出現次數
            $datas[$key]['monthly_pet_counts'] = array_count_values($datas[$key]['pets']['name']);
        }
        
        // 排序寶貝名稱，基於數量
        arsort($petCounts);
        
        // 取出前三名
        $topThreePets = array_slice($petCounts, 0, 3);
        
        // 將整體排名前三的寶貝添加到 $datas 陣列
        $datas['top_three_pets'] = $topThreePets;
        
        // 現在 $datas 陣列包含了每個月寶貝名稱的出現次數和整體排名前三的寶貝名稱及其次數
        

        dd($datas);
        return view('rpg19.index')->with('datas',$datas)
                                  ->with('request',$request)
                                  ->with('search_year',$search_year)
                                  ->with('years',$years)
                                  ->with('sums',$sums);
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
}
