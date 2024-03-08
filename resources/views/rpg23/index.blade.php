@extends('layouts.vertical', ["page_title"=> "客戶分佈報表"])

@section('content')
<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Huaxixiang</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">報表管理</a></li>
                        <li class="breadcrumb-item active">客戶分佈報表</li>
                    </ol>
                </div>
                <h4 class="page-title">客戶分佈報表（共{{$total_count}}人）</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    @foreach($datas as $county_name => $district_datas)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4>{{ $county_name }}（共{{ $sums[$county_name]['count'] }}人）</h4>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                            @foreach($district_datas as $district_count => $districts)
                                <!-- 將 districts 分成每三個一組 -->
                                @foreach(collect($districts)->chunk(5) as $chunkedDistricts)
                                    <tr>
                                        @foreach($chunkedDistricts as $districtname => $district)
                                            <td>{{ $districtname }}</td>
                                            <td>
                                                <a href="{{ route('rpg23.detail',['district'=>$districtname]) }}">{{ $district['count'] }}</a>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
 


</div> <!-- container -->
@endsection