@extends('layouts.vertical', ['page_title' => '每月金紙報表'])

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
                            <li class="breadcrumb-item active">每月金紙報表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">每月金紙報表</h4>
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
                                <form class="d-flex flex-wrap align-items-center" id="myForm"
                                    action="{{ route('rpg13') }}" method="GET">
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
                                    {{-- <label for="status-select" class="me-2">來源類別</label> --}}
                                    {{-- <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="source" onchange="this.form.submit()">
                                        <option value="NULL">不限</option>
                                        @foreach ($sources as $source)
                                            <option value="{{$source->code}}" @if ($request->source == $source->code) selected @endif >{{ $source->name }}</option>
                                        @endforeach
                                    </select>
                                </div> --}}
                                    <div class="me-3">
                                        <button type="submit" onclick="CheckSearch(event)"
                                            class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                </form>
                            </div>
                            {{-- <div class="col-auto">
                            <div class="text-lg-end my-1 my-lg-0">
                                <h3><span class="text-danger">共計：{{ number_format($sums['count']) }}單，傭金共{{ number_format($sums['commission']) }}元</span></h3>
                            </div>
                        </div><!-- end col--> --}}
                        </div> <!-- end row -->
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col-->
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>總賣出數量</h4>
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                @foreach ($products as $key => $product)
                                    @if (($key + 1) % 5 == 1)
                                        <tr>
                                    @endif
                                    <td>{{ $datas['products'][$product->id]['name'] }}</td>
                                    <td>
                                        @if (isset($datas['products'][$product->id]))
                                            {{ $datas['products'][$product->id]['num'] }}
                                        @else
                                            0
                                        @endif
                                    </td>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>一般金紙賣出數量</h4>
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead>
                                    @foreach ($products as $key => $product)
                                        @if (($key + 1) % 5 == 1)
                                            <tr>
                                        @endif
                                        <td>{{ $product['name'] }}</td>
                                        <td>
                                            @if (isset($datas['normals'][$product->id]))
                                                {{ $datas['normals'][$product->id]['num'] }}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @endforeach
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>套組金紙賣出數量</h4>
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead>
                                    @foreach ($datas['sets'] as $key => $combo)
                                        <tr class="table-light">
                                            <td colspan="13">{{ $combo['name'] }}（共{{ $combo['count'] }}個）</td>
                                        </tr>
                                        <td></td>
                                        @foreach ($combo['details'] as $detail_key => $detail)
                                            @if (count($combo['details']) % 6 == 1)
                                                <tr>
                                            @endif
                                            <td>{{ $detail['name'] }}</td>
                                            <td>{{ $detail['num'] }}</td>
                                        @endforeach
                                    @endforeach
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>組合金紙賣出數量</h4>
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead>
                                    @foreach ($datas['combos'] as $key => $combo)
                                        <tr class="table-light">
                                            <td colspan="12">{{ $combo['name'] }}（{{ $combo['count'] }}個）</td>
                                        </tr>
                                        @foreach ($combo['details'] as $detail_key => $detail)
                                            @if (count($combo['details']) % 6 == 1)
                                                <tr>
                                            @endif
                                            <td>{{ $detail['name'] }}</td>
                                            <td>{{ $detail['num'] }}</td>
                                        @endforeach
                                    @endforeach
                                </thead>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>法會金紙賣出數量</h4>
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead>
                                    @foreach ($datas['pujas'] as $key => $puja)
                                        <tr class="table-light">
                                            <td colspan="4">
                                                @if (isset($puja['name']))
                                                    {{ $puja['name'] }}（共{{ $puja['count'] }}個）
                                                    @else
                                                    {{ $key}}
                                                @endif
                                                
                                            </td>
                                        </tr>
                                        @if (isset($puja['details']))
                                            @foreach ($puja['details'] as $detail_key => $detail)
                                                @if (count($puja['details']) % 2 == 1)
                                                    <tr>
                                                @endif
                                                <td>{{ $detail['name'] }}</td>
                                                <td>{{ $detail['num'] }}</td>
                                            @endforeach
                                        @endif
                                        <tr class="table-light">
                                            <td colspan="4">額外加購</td>
                                        </tr>
                                        @foreach ($puja['attachs'] as $attach_key => $attach)
                                            <tr>
                                                <td colspan="3">
                                                    @if (isset($attach['name']))
                                                        {{ $attach['name'] }}
                                                    @endif
                                                </td>
                                                <td>{{ $attach['num'] }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- container -->
@endsection
