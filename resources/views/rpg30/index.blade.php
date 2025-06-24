@extends('layouts.vertical', ['page_title' => '月/季獎金統計'])

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
                            <li class="breadcrumb-item active">月/季獎金統計</li>
                        </ol>
                    </div>
                    <h4 class="page-title">月/季獎金統計</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between">
                            <div class="col-auto">
                                <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg30') }}"
                                    method="GET">
                                    <label for="status-select" class="me-2">年度</label>
                                    <div class="me-sm-3">
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="year"
                                            onchange="this.form.submit()">
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}"
                                                    @if ($request->year == $year) selected @endif>{{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="me-3">
                                        <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                </form>
                            </div>
                        </div> <!-- end row -->
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col-->
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead class="table-light">
                                    <tr align="center">
                                        <th scope="col">月份</th>
                                        <th scope="col">金紙總額</th>
                                        <th scope="col">花樹葬總數</th>
                                        <th scope="col">盆栽總數</th>
                                        <th scope="col">美化總數</th>
                                        <th scope="col">平安燈總數</th>
                                        <th scope="col">骨灰罐總額</th>
                                        <th scope="col">指定款總額</th>
                                        <th scope="col">火化套裝（季）</th>
                                        <th scope="col">骨灰罐＋紀念品（季）</th>
                                    </tr>
                                </thead>
                                <tbody align="center">
                                    @foreach ($datas as $key => $data)
                                        <tr>

                                            <td>{{ $data['month'] }}</td>
                                            <td><a href="{{ route('rpg30.detail', ['year'=>$request->year ,'month'=>$key, 'type'=>'gdpaper']) }}">{{number_format($data['gdpaper_month']) }}</a></td>
                                            <td><a href="{{ route('rpg30.detail', ['year'=>$request->year ,'month'=>$key, 'type'=>'flower']) }}">{{ number_format($data['flower_month']) }}</a></td>
                                            <td><a href="{{ route('rpg30.detail', ['year'=>$request->year ,'month'=>$key, 'type'=>'potted_plant']) }}">{{ number_format($data['potted_plant_month']) }}</a></td>
                                            <td><a href="{{ route('rpg30.detail', ['year'=>$request->year ,'month'=>$key, 'type'=>'beautify']) }}">{{ number_format($data['beautify_month']) }}</a></td>
                                            <td><a href="{{ route('rpg30.detail', ['year'=>$request->year ,'month'=>$key, 'type'=>'lamp']) }}">{{ number_format($data['lamp_month']) }}</a></td>
                                            <td><a href="{{ route('rpg30.detail', ['year'=>$request->year ,'month'=>$key, 'type'=>'urn']) }}">{{ number_format($data['urn_month']) }}</a></td>
                                            <td><a href="{{ route('rpg30.detail', ['year'=>$request->year ,'month'=>$key, 'type'=>'specify']) }}">{{ number_format($data['specify_month']) }}</a></td>
                                            {{-- 只在每 3 筆的第一筆輸出合併 cell --}}
                                            @if ($loop->iteration % 3 === 1)
                                                <td rowspan="3">
                                                    @if (isset($season_datas[$key]['suit_seasons']))
                                                        @foreach ($season_datas[$key]['suit_seasons'] as $suit_season)
                                                            {{ $suit_season['name'] }}：{{ number_format($suit_season['count']) }}個<br>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td rowspan="3">
                                                    @if (isset($season_datas[$key]['urn_souvenir_season']))
                                                        {{ number_format($season_datas[$key]['urn_souvenir_season']) }}
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div> <!-- container -->
@endsection
