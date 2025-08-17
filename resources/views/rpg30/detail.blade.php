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
                    <h4 class="page-title">
                        @if (isset($season_start) && isset($season_end))
                            {{ $season_start . '~' . $season_end }}
                        @else
                            {{ $year . '-' . $month }}
                        @endif
                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead>
                                    <tr align="center">
                                        <td>No</td>
                                        <td>日期</td>
                                        <td>客戶名稱</td>
                                        <td>寵物名稱</td>
                                        @if ($type == 'gdpaper')
                                            <td>金紙</td>
                                            <td>合計</td>
                                        @elseif ($type == 'lamp')
                                            <td>金紙</td>
                                            <td>合計</td>
                                        @elseif ($type == 'urn')
                                            <td>合計</td>
                                        @elseif ($type == 'specify')
                                            <td>明細</td>
                                            <td>合計</td>
                                        @elseif ($type == 'urn_souvenir')
                                            <td>品項</td>
                                            <td>合計</td>
                                        @endif

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $key => $sale)
                                        <tr align="center">
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $sale->sale_date }}</td>
                                            <td>{{ $sale->cust_name->name }}</td>
                                            <td>{{ $sale->pet_name }}</td>
                                            @if ($type == 'gdpaper')
                                                <td>
                                                    @foreach ($sale->gdpapers as $gdpaper)
                                                        {{ $gdpaper->gdpaper_name->name }}：{{ number_format($gdpaper->gdpaper_total) }}<br>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    {{ number_format($sale->gdpapers->sum('gdpaper_total')) }}
                                                </td>
                                            @elseif ($type == 'lamp')
                                                <td>
                                                    @foreach ($sale->proms as $prom)
                                                        @if ($prom->prom_type == 'C')
                                                            @if (isset($prom->prom_id))
                                                                {{ $prom->prom_name->name }}-{{ number_format($prom->prom_total) }}<br>
                                                            @else
                                                                無
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>{{ number_format($sale->proms->where('prom_type', 'C')->count()) }}
                                                </td>
                                            @elseif ($type == 'urn')
                                                <td>
                                                    @foreach ($sale->proms as $prom)
                                                        @if ($prom->prom_type == 'A')
                                                            @if (isset($prom->prom_id))
                                                                {{ number_format($prom->prom_total) }}<br>
                                                            @else
                                                                無
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>
                                            @elseif ($type == 'specify')
                                                <td>
                                                    @foreach ($sale->proms as $prom)
                                                        @if (isset($prom->prom_id))
                                                            {{ $prom->prom_name->name }}-{{ number_format($prom->prom_total) }}<br>
                                                        @else
                                                            無
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>
                                                    {{ number_format($sale->proms->sum('prom_total')) }}
                                                </td>
                                            @elseif ($type == 'urn_souvenir')
                                                <td>
                                                    @foreach ($sale->proms as $prom)
                                                        @if (isset($prom->prom_id))
                                                            {{ $prom->prom_name->name }}-{{ number_format($prom->prom_total) }}<br>
                                                        @else
                                                            無
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>
                                                    {{ number_format($sale->proms->sum('prom_total')) }}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            <br>
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center mb-3">
                                        <button type="reset" class="btn btn-secondary waves-effect waves-light m-1"
                                            onclick="history.go(-1)">回上一頁</button>
                                    </div>
                                </div> <!-- end col -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
