<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.shared/title-meta', ['title' => $page_title])

    @include('layouts.shared/head-css', ["mode" => $mode ?? '', "demo" => $demo ?? ''])

</head>


<body class="loading" data-layout='{"mode": "{{$theme ?? "light" }}", "width": "fluid", "menuPosition": "fixed", "sidebar": { "color": "{{$theme ?? "light" }}", "size": "default", "showuser": false}, "topbar": {"color": "dark"}, "showRightSidebarOnPageLoad": true}' @yield('body-extra')>
    <!-- Begin page -->
    <div id="wrapper">
        @include('layouts.shared/topbar')

        @if(Auth::user()->status == 0)<!--用戶是否啟用-->
            @if(Auth::user()->job_id == 1)<!-- 老闆(1)-->
                @include('layouts.shared/admin-left-sidebar')
            @elseif(Auth::user()->job_id == 2)<!-- 行政主管(2) -->

            @elseif(Auth::user()->job_id == 3)<!-- 專員主管(3) -->

            @elseif(Auth::user()->job_id == 4)<!-- 行政(4) -->

            @elseif(Auth::user()->job_id == 5)<!-- 專員(5) -->

            @elseif(Auth::user()->job_id == 6)<!-- 股東(6) -->

            @endif
        @endif

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                @yield('content')
            </div>
            <!-- content -->

            @include('layouts.shared/footer')

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    {{-- @include('layouts.shared/right-sidebar') --}}

    @include('layouts.shared/footer-script')

</body>

</html>