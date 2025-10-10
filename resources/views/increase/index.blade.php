@extends('layouts.vertical', ["page_title"=> "加成列表"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">加成管理</a></li>
                        <li class="breadcrumb-item active">加成列表</li>
                    </ol>
                </div>
                <h4 class="page-title">加成列表</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('increase.index') }}" method="GET">
                                <div class="me-3">
                                    <label for="start_date" class="form-label">開始日期</label>
                                    <input type="date" class="form-control my-1 my-lg-0" name="start_date" value="{{ $request->start_date ?? '' }}">
                                </div>
                                <div class="me-3">
                                    <label for="end_date" class="form-label">結束日期</label>
                                    <input type="date" class="form-control my-1 my-lg-0" name="end_date" value="{{ $request->end_date ?? '' }}">
                                </div>
                                <div class="me-3 mt-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                    <a href="{{ route('increase.index') }}" class="btn btn-secondary waves-effect waves-light me-1"><i class="fe-refresh-cw me-1"></i>重置</a>
                                </div>
                            </form>
                        </div>
                        <div class="col mt-3">
                            <div class="text-lg-end my-1 my-lg-0 mt-5">
                                <a href="{{ route('increase.create') }}" class="btn btn-danger waves-effect waves-light me-1"><i class="mdi mdi-plus-circle me-1"></i>新增加成</a>
                                <a href="{{ route('increase.export') }}?start_date={{ $request->start_date ?? '' }}&end_date={{ $request->end_date ?? '' }}" class="btn btn-info waves-effect waves-light"><i class="mdi mdi-download me-1"></i>匯出Excel</a>
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
                        @if($datas->count() > 0)
                            @foreach ($datas as $data)
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <h5 class="mb-0">
                                                    <i class="mdi mdi-calendar me-2"></i>
                                                    {{ $data->increase_date->format('Y年m月d日') }} ({{ $data->increase_date->format('l') }})
                                                </h5>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <div class="btn-group">
                                                    <a href="{{ route('increase.edit',$data->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="mdi mdi-pencil me-1"></i>編輯
                                                    </a>
                                                    <a href="{{ route('increase.del',$data->id) }}" class="btn btn-sm btn-outline-danger">
                                                        <i class="mdi mdi-delete me-1"></i>刪除
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @if($data->comment)
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="mdi mdi-note-text me-1"></i>備註：{{ $data->comment }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body p-0">
                                        @php
                                            // 直接顯示每個項目，不按人員分組統計
                                            $displayItems = [];
                                            foreach ($data->items as $item) {
                                                $displayItem = [
                                                    'id' => $item->id,
                                                    'item_type' => $item->item_type,
                                                    'person_name' => '',
                                                    'categories' => [],
                                                    'phone_amount' => 0,
                                                    'receive_amount' => 0,
                                                    'furnace_amount' => 0,
                                                    'overtime_amount' => 0,
                                                    'total_amount' => 0,
                                                    'phone_exclude_bonus' => false
                                                ];
                                                
                                                // 處理接電話人員
                                                if ($item->phone_person_id) {
                                                    $displayItem['person_name'] = $item->phonePerson->name ?? '未指定';
                                                    $displayItem['phone_amount'] = $item->phone_exclude_bonus ? 0 : $item->total_phone_amount;
                                                    $displayItem['phone_exclude_bonus'] = $item->phone_exclude_bonus;
                                                    
                                                    // 記錄類別
                                                    if ($item->night_phone_amount > 0) $displayItem['categories'][] = '夜間';
                                                    if ($item->evening_phone_amount > 0) $displayItem['categories'][] = '晚間';
                                                    if ($item->typhoon_phone_amount > 0) $displayItem['categories'][] = '颱風';
                                                }
                                                
                                                // 處理接件人員
                                                if ($item->receive_person_id) {
                                                    if (empty($displayItem['person_name'])) {
                                                        $displayItem['person_name'] = $item->receivePerson->name ?? '未指定';
                                                    }
                                                    $displayItem['receive_amount'] = $item->total_receive_amount;
                                                    
                                                    // 記錄類別
                                                    if ($item->night_receive_amount > 0) $displayItem['categories'][] = '夜間';
                                                    if ($item->evening_receive_amount > 0) $displayItem['categories'][] = '晚間';
                                                    if ($item->typhoon_receive_amount > 0) $displayItem['categories'][] = '颱風';
                                                }
                                                
                                                // 處理夜間開爐人員
                                                if ($item->furnace_person_id) {
                                                    if (empty($displayItem['person_name'])) {
                                                        $displayItem['person_name'] = $item->furnacePerson->name ?? '未指定';
                                                    }
                                                    $displayItem['furnace_amount'] = $item->total_amount;
                                                    $displayItem['categories'][] = '夜間開爐';
                                                }
                                                
                                                // 處理加班費人員
                                                if ($item->overtime_record_id) {
                                                    $displayItem['person_name'] = $item->overtimeRecord->user->name ?? '未指定';
                                                    $displayItem['overtime_amount'] = $item->custom_amount ?? $item->total_amount;
                                                    $displayItem['categories'][] = '加班費';
                                                    
                                                    // 加班費項目不應該有接電話、接件、夜間開爐獎金
                                                    $displayItem['phone_amount'] = 0;
                                                    $displayItem['receive_amount'] = 0;
                                                    $displayItem['furnace_amount'] = 0;
                                                }
                                                
                                                // 去重複類別
                                                $displayItem['categories'] = array_unique($displayItem['categories']);
                                                $displayItem['categories'] = array_values($displayItem['categories']);
                                                
                                                // 計算總計
                                                $displayItem['total_amount'] = $displayItem['phone_amount'] + $displayItem['receive_amount'] + $displayItem['furnace_amount'] + $displayItem['overtime_amount'];
                                                
                                                $displayItems[] = $displayItem;
                                            }
                                        @endphp
                                        
                                        @if(count($displayItems) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                                            <th style="width: 18%;">人員</th>
                                                            <th style="width: 22%;">加成類別</th>
                                                            <th style="width: 12%;">接電話</th>
                                                            <th style="width: 12%;">接件</th>
                                                            <th style="width: 12%;">夜間開爐</th>
                                                            <th style="width: 12%;">加班費</th>
                                                            <th style="width: 12%;">總計</th>
                                    </tr>
                                </thead>
                                <tbody>
                                                        @foreach ($displayItems as $item)
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                                            <span class="avatar-title text-white font-weight-bold">
                                                                                {{ mb_substr($item['person_name'], 0, 1) }}
                                                                            </span>
                                                                        </div>
                                                                        <div>
                                                                            <h6 class="mb-0">{{ $item['person_name'] }}</h6>
                                                                            @if($item['phone_exclude_bonus'])
                                                                                <small class="text-muted">(接電話不計入獎金)</small>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @if(count($item['categories']) > 0)
                                                                        @foreach ($item['categories'] as $category)
                                                                            @php
                                                                                $badgeClass = match($category) {
                                                                                    '夜間' => 'bg-primary',
                                                                                    '晚間' => 'bg-success', 
                                                                                    '颱風' => 'bg-warning',
                                                                                    '加班費' => 'bg-info',
                                                                                    '夜間開爐' => 'bg-secondary',
                                                                                    default => 'bg-light text-dark'
                                                                                };
                                    @endphp
                                                                            <span class="badge {{ $badgeClass }} me-1 mb-1">{{ $category }}</span>
                                                                        @endforeach
                                                                    @else
                                                                        <span class="text-muted">無</span>
                                                                    @endif
                                                    </td>
                                                                <td>
                                                                    @if($item['phone_amount'] > 0)
                                                                        <span class="text-primary fw-bold">${{ number_format($item['phone_amount'], 0) }}</span>
                                                                    @else
                                                                        <span class="text-muted">$0</span>
                                                @endif
                                                                </td>
                                                                <td>
                                                                    @if($item['receive_amount'] > 0)
                                                                        <span class="text-success fw-bold">${{ number_format($item['receive_amount'], 0) }}</span>
                                                                    @else
                                                                        <span class="text-muted">$0</span>
                                                @endif
                                                                </td>
                                                                <td>
                                                                    @if($item['furnace_amount'] > 0)
                                                                        <span class="text-secondary fw-bold">${{ number_format($item['furnace_amount'], 0) }}</span>
                                                                    @else
                                                                        <span class="text-muted">$0</span>
                                                @endif
                                                </td>
                                                <td>
                                                                    @if($item['overtime_amount'] > 0)
                                                                        <span class="text-info fw-bold">${{ number_format($item['overtime_amount'], 0) }}</span>
                                                                    @else
                                                                        <span class="text-muted">$0</span>
                                                    @endif
                                                </td>
                                                                <td>
                                                                    <span class="text-dark fw-bold fs-6">${{ number_format($item['total_amount'], 0) }}</span>
                                                </td>
                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="table-light">
                                                        <tr>
                                                            <td colspan="6" class="text-end fw-bold">當日總計：</td>
                                                            <td class="fw-bold fs-5 text-primary">
                                                                ${{ number_format(array_sum(array_column($displayItems, 'total_amount')), 0) }}
                                                </td>
                                            </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="mdi mdi-information-outline text-muted" style="font-size: 48px;"></i>
                                                <p class="text-muted mt-2">此日期無加成記錄</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                    @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="mdi mdi-calendar-remove-outline text-muted" style="font-size: 64px;"></i>
                                <h4 class="text-muted mt-3">沒有找到加成記錄</h4>
                                <p class="text-muted">請調整搜尋條件或建立新的加成記錄</p>
                                <a href="{{ route('increase.create') }}" class="btn btn-primary">
                                    <i class="mdi mdi-plus me-1"></i>新增加成記錄
                                </a>
                            </div>
                        @endif
                            <br>
                            <ul class="pagination pagination-rounded justify-content-end mb-0">
                                {{ $datas->links('vendor.pagination.bootstrap-4') }}
                            </ul>
                        </div>
                    </div>
                </div>
                </div>
            </div>

                    

</div> <!-- container -->
@endsection