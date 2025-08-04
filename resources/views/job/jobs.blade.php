@extends('layouts.vertical', ["page_title"=> "CRM Customers"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">用戶管理</a></li>
                        <li class="breadcrumb-item active">職稱列表</li>
                    </ol>
                </div>
                <h4 class="page-title">職稱列表</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div id="standard-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="standard-modalLabel">Modal Heading</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Text in a modal</h6>
                    <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula.</p>
                    <hr>
                    <h6>Overflowing text to show scroll behavior</h6>
                    <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                    <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
                    <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-8">
                            {{-- <div class="mt-2 mt-sm-0">
                                <button type="button" class="btn btn-success mb-2 me-1"><i class="fe-search me-1"></i>搜尋</button>
                            </div> --}}
                        </div><!-- end col-->
                        <div class="col-sm-4 text-sm-end">
                            <a href="{{ route('job.create') }}">
                                <button type="button" class="btn btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#custom-modal"><i class="mdi mdi-plus-circle me-1"></i>新增職稱</button>
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>編號</th>
                                    <th>職稱名稱</th>
                                    <th>主管名稱</th>
                                    <th>使用人數</th>
                                    <th>狀態</th>
                                    <th>動作</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($datas as $key=>$data)
                                @php
                                    // Debug: 輸出職稱資訊
                                    \Log::info('職稱人數檢查:', [
                                        'job_id' => $data->id,
                                        'job_name' => $data->name,
                                        'active_count' => $data->active_user_count,
                                        'total_count' => $data->total_user_count,
                                        'users_loaded' => $data->relationLoaded('users') ? 'yes' : 'no'
                                    ]);
                                @endphp
                                <tr>
                                    <td>{{ $data->id }}</td>
                                    <td><span style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#bs-example-modal-lg">{{ $data->name }}</span></td>
                                    <td>
                                        @if(isset($data->director_id)) {{ $data->director_data->name }}
                                        @else 無
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $active_count = $data->active_user_count;
                                        @endphp
                                        @if($active_count > 0)
                                            <span class="badge bg-primary">{{ $active_count }} 人</span>
                                        @else
                                            <span class="text-muted">0 人</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($data->status == "up") 啟用
                                        @else <b style="color:red;">停用</b>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('job.edit',$data->id) }}" class="action-icon"> <i class="mdi mdi-square-edit-outline"></i></a>
                                        {{-- <a href="javascript:void(0);" class="action-icon"> <i class="mdi mdi-delete"></i></a> --}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <br>
                        <ul class="pagination pagination-rounded justify-content-end mb-0">
                            {{ $datas->links('vendor.pagination.bootstrap-4') }}
                        </ul>
                    </div>

                    
                    <div class="modal fade" id="bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myLargeModalLabel">Large modal</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ...
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <!-- end row -->

</div> <!-- container -->
@endsection