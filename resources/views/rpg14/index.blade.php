@extends('layouts.vertical', ['page_title' => '每月來源報表'])

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
                            <li class="breadcrumb-item active">每月來源報表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">每月來源報表</h4>
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
                                <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg14') }}"
                                    method="GET">
                                    <label for="status-select" class="me-2">年度</label>
                                    <div class="me-sm-3">
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="year"
                                            onchange="this.form.submit()">
                                            <option value="" selected>不限</option>
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}"
                                                    @if ($request->year == $year) selected @endif>{{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label for="status-select" class="me-2">月份</label>
                                    <div class="me-sm-3">
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="month"
                                            onchange="this.form.submit()">
                                            <option value="" selected>請選擇</option>
                                            <option value="01" @if ($request->month == '01') selected @endif>一月
                                            </option>
                                            <option value="02" @if ($request->month == '02') selected @endif>二月
                                            </option>
                                            <option value="03" @if ($request->month == '03') selected @endif>三月
                                            </option>
                                            <option value="04" @if ($request->month == '04') selected @endif>四月
                                            </option>
                                            <option value="05" @if ($request->month == '05') selected @endif>五月
                                            </option>
                                            <option value="06" @if ($request->month == '06') selected @endif>六月
                                            </option>
                                            <option value="07" @if ($request->month == '07') selected @endif>七月
                                            </option>
                                            <option value="08" @if ($request->month == '08') selected @endif>八月
                                            </option>
                                            <option value="09" @if ($request->month == '09') selected @endif>九月
                                            </option>
                                            <option value="10" @if ($request->month == '10') selected @endif>十月
                                            </option>
                                            <option value="11" @if ($request->month == '11') selected @endif>十一月
                                            </option>
                                            <option value="12" @if ($request->month == '12') selected @endif>十二月
                                            </option>
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
                                        <th scope="col" width="15%">日期</th>
                                        @foreach ($sources as $key => $source)
                                            <th scope="col">{{ $source->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr align="center" style="color:red;font-weight:500;">
                                        <td>總單量</td>
                                        @foreach ($sources as $key => $source)
                                            @if (isset($sums[$source->code]['count']) && $sums[$source->code]['count'] != 0)
                                                <td>
                                                    @if (in_array($source->code, ['H', 'Salon', 'B', 'G', 'dogpark', 'other']))
                                                        <a
                                                            href="{{ route('rpg14.month.detail', ['month' => $month, 'source_code' => $source->code]) }}">{{ $sums[$source->code]['count'] }}</a>單
                                                    @else
                                                        {{ $sums[$source->code]['count'] }}單
                                                    @endif
                                                </td>
                                            @else
                                                <td>0單</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                    @foreach ($datas as $key => $data)
                                        <tr align="center">
                                            <td>{{ $key }}</td>
                                            @foreach ($sources as $source)
                                                @if (isset($data[$source->code]['count']) && $data[$source->code]['count'] != 0)
                                                    <td><a
                                                            href="{{ route('rpg14.detail', ['date' => $key, 'source_code' => $source->code]) }}">{{ $data[$source->code]['count'] }}</a>
                                                    </td>
                                                @else
                                                    <td></td>
                                                @endif
                                            @endforeach
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
