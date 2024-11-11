@extends('layouts.vertical', ['page_title' => '每月來源統計'])

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
                            <li class="breadcrumb-item active">每月來源統計</li>
                        </ol>
                    </div>
                    <h4 class="page-title">每月來源統計</h4>
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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap table-hover mb-0">
                                    <thead class="table-light">
                                        <tr align="center">
                                            <th>月份</th>
                                            @foreach ($datas as $key => $data)
                                                <th colspan="1">
                                                    @if(isset( $data['name'])){{ $data['name'] }}@endif
                                                </th>
                                            @endforeach
                                            {{-- <tr align="center" class="text-danger">
                                        <th>總計</th>
                                        @foreach ($datas as $key => $data)
                                            <th>{{ $sums['count'] }}個</th>
                                            <th>{{ number_format($sums[$key]['total_price']) }}</th>
                                        @endforeach
                                    </tr> --}}
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($months as $key => $month)
                                            <tr align="center">
                                                <td>{{ $month['monthName'] }}</td>
                                                @foreach ($datas as $source_id => $data)
                                                    <td>
                                                        @if(isset($data['name']))
                                                        <a
                                                            href="{{ route('rpg27.detail', ['year' => $request->year, 'month' => $key + 1, 'source_id' => $source_id]) }}">
                                                            {{ number_format($data['months'][$key]['count']) }}
                                                        </a>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col -->
            </div>
            <!-- end row -->

        </div> <!-- container -->
    @endsection
