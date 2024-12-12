@extends('layouts.app')

@section('main-content')
    <div class="pagetitle">
        <h1>報表管理</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">報表管理</li>
                <li class="breadcrumb-item active">每月方案報表</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <form action="{{ route('rpg01') }}" method="get">
            @csrf
            <div class="row col-lg-12 mb-4 mt-4">
                <div class="col-auto">
                    <label for="after_date">年度</label>
                    <select id="inputState" class="form-select" name="year" onchange="this.form.submit()">
                        @foreach($years as $year)
                        <option value="{{$year}}" @if($request->year == $year) selected @endif >{{ $year }}年</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label for="after_date">月份</label>
                    <select id="inputState" class="form-select" name="month" onchange="this.form.submit()">
                        <option value="" selected >請選擇</option>
                        <option value="01" @if($request->month == "01" ) selected  @endif>一月</option>
                        <option value="02" @if($request->month == "02" ) selected  @endif>二月</option>
                        <option value="03" @if($request->month == "03" ) selected  @endif>三月</option>
                        <option value="04" @if($request->month == "04" ) selected  @endif>四月</option>
                        <option value="05" @if($request->month == "05" ) selected  @endif>五月</option>
                        <option value="06" @if($request->month == "06" ) selected  @endif>六月</option>
                        <option value="07" @if($request->month == "07" ) selected  @endif>七月</option>
                        <option value="08" @if($request->month == "08" ) selected  @endif>八月</option>
                        <option value="09" @if($request->month == "09" ) selected  @endif>九月</option>
                        <option value="10" @if($request->month == "10" ) selected  @endif>十月</option>
                        <option value="11" @if($request->month == "11" ) selected  @endif>十一月</option>
                        <option value="12" @if($request->month == "12" ) selected  @endif>十二月</option>
                    </select>
                </div>
                <div class="col-auto">
                    <div style="margin-top: 22px;">
                        <label for="after_date">&nbsp;</label>
                        <button type="submit" class="btn btn-danger">查詢</button>
                    </div>
                </div>
            </div>
        </form>
        <!-- Left side columns -->
        <div class="col-lg-12 mx-auto">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"></h5>

                        <!-- Table with hoverable rows -->
                        <table class="table table-hover">
                            <thead>
                                <tr align="center">
                                    <th scope="col" width="15%">日期</th>
                                    @foreach ($plans as $key => $plan)
                                        <th scope="col">{{ $plan->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datas as $key => $data)
                                    <tr align="center">
                                        <td>{{ $key }}</td>
                                        @foreach ($plans as  $plan)
                                            @if (isset($data[$plan->id]['count']) && $data[$plan->id]['count'] != 0)
                                                <td><a href="{{ route('rpg01.detail',['date'=>$key,'plan_id'=>$plan->id]) }}">{{ $data[$plan->id]['count'] }}</a></td>
                                            @else
                                                <td></td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr align="center" style="color:red;font-weight:500;">
                                    <td>總單量</td>
                                    @foreach ($plans as $key => $plan)
                                        @if (isset($sums[$plan->id]['count']) && $sums[$plan->id]['count'] != 0)
                                            <td>{{ $sums[$plan->id]['count'] }}單</td>
                                        @else
                                            <td>0單</td>
                                        @endif
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                        <!-- End Table with hoverable rows -->
                    </div>

                </div>
            </div>
        </div>
        </div><!-- End News & Updates -->

        </div><!-- End Right side columns -->

    </section>
@endsection
