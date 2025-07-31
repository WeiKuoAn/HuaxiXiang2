@extends('layouts.vertical', ['page_title' => '年度來源統計'])

@php
    use Carbon\Carbon;
@endphp

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
                            <li class="breadcrumb-item active">年度來源統計</li>
                        </ol>
                    </div>
                    <h4 class="page-title">年度來源統計</h4>
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
                                <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg27') }}"
                                    method="GET">
                                    <label for="status-select" class="me-2">年度</label>
                                    <div class="me-sm-3">
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="year"
                                            onchange="this.form.submit()">
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}"
                                                    @if ($request->year == $year) selected @endif>{{ $year }}年
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
                            <div class="col-auto">
                                <div class="text-lg-end my-1 my-lg-0">
                                    {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                </div>
                            </div><!-- end col-->
                        </div> <!-- end row -->
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col-->
        </div>

        @foreach ($datas as $source_id => $data)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4>{{ $data['name'] }}（共{{ $data['count'] }}件）</h4>
                            <div class="row mb-2">
                                <div class="table-responsive">
                                    <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                        @php
                                            $count = 0;
                                        @endphp
                                        @foreach ($data['items'] as $key => $item)
                                            @if (isset($item['name']))
                                                @if ($count % 5 == 0)
                                                    <tr>
                                                @endif
                                                <td>{{ $item['name'] }}</td>
                                                <td><a href="{{ route('rpg27.detail', ['year' => $request->year ?: Carbon::now()->year, 'month' => $request->month ?: 'all', 'source_id' => $source_id, 'company_id' => $key]) }}">{{ $item['count'] }}</a></td>
                                                @php
                                                    $count++;
                                                @endphp
                                                @if ($count % 5 == 0)
                                                    </tr>
                                                @endif
                                            @endif
                                        @endforeach
                                        @if ($count % 5 != 0)
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col -->
                </div>
            </div>
        @endforeach
    </div>
@endsection
