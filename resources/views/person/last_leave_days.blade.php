@extends('layouts.vertical', ['page_title' => '個人假別剩餘天數'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">請假管理</a></li>
                            <li class="breadcrumb-item active">個人假別剩餘天數</li>
                        </ol>
                    </div>
                    <h4 class="page-title">個人假別剩餘天數</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="table-light">
                                    <tr align="center">
                                        <td>假別名稱</td>
                                        <td>可休總天數</td>
                                        <td>剩餘天數</td>
                                    </tr>
                                    @foreach ($dates as $date_type => $date)
                                        <tr align="center">
                                            <td>{{ $date['name'] }}</td>
                                            @if ($date['name'] == '特休')
                                                <td align="center">{{ $date['user_day'][Auth::user()->id]['day'] }}天</td>
                                            @else
                                                <td align="center">{{ $date['day'] }}天</td>
                                            @endif
                                            <td align="center" @if (
                                                $leave_datas[Auth::user()->id]['leavedays'][$date_type]['day'] == '0天，又0小時' ||
                                                    $leave_datas[Auth::user()->id]['leavedays'][$date_type]['day'] == '0天') style="color: red" @endif>
                                                {{ $leave_datas[Auth::user()->id]['leavedays'][$date_type]['day'] }}
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr align="center">
                                        @foreach ($dates as $date_type => $date)
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div> <!-- end row -->
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col-->
        </div>


    </div> <!-- container -->
@endsection
