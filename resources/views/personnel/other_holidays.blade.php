@extends('layouts.vertical', ['page_title' => '其他假總覽'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">人事管理</a></li>
                            <li class="breadcrumb-item active">其他假總覽</li>
                        </ol>
                    </div>
                    <h4 class="page-title">其他假總覽</h4>
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
                                <form class="d-flex flex-wrap align-items-center"
                                    action="{{ route('personnel.other_holidays') }}" method="GET">
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

                            </div><!-- end col-->
                        </div> <!-- end row -->
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col-->
        </div>

        <div class="row">
            @foreach ($datas as $user_id => $data)
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="header-title mb-3">
                                @if (isset($data['name']))
                                    {{ $data['name'] }}
                                @endif
                            </h3>

                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap table-hover mb-0">

                                    <thead class="table-light">
                                        <tr align="center">
                                            <th>假別</th>
                                            <th>累積天數</th>
                                            <th>剩餘天數</th>
                                            <th>總天數</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dates as $date_type => $date)
                                            <tr>
                                                <td>
                                                    @if (isset($date['name']))
                                                        {{ $date['name'] }}
                                                    @endif
                                                </td>
                                                <td align="center">{{ $data['leavedays'][$date_type]['add_day'] }}</td>
                                                <td align="center"
                                                    @if ($data['leavedays'][$date_type]['day'] == '0天，又0小時' || $data['leavedays'][$date_type]['day'] == '0天') style="color: red" @endif>
                                                    {{ $data['leavedays'][$date_type]['day'] }}
                                                </td>
                                                @if (isset($date['name']))
                                                    @if (isset($date['name']) && $date['name'] == '特休')
                                                        <td align="center">{{ $date['user_day'][$user_id]['day'] }}天</td>
                                                    @else
                                                        <td align="center">{{ $date['day'] }}天</td>
                                                    @endif
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> <!-- end .table-responsive-->
                        </div>
                    </div> <!-- end card-->
                </div> <!-- end col -->
            @endforeach
        </div>
    </div> <!-- container -->
@endsection
