<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.shared/title-meta', ['title' => $page_title])

    @include('layouts.shared/head-css', ['mode' => $mode ?? '', 'demo' => $demo ?? ''])

</head>


<body class="loading"
    data-layout='{"mode": "{{ $theme ?? 'light' }}", "width": "fluid", "menuPosition": "fixed", "sidebar": { "color": "{{ $theme ?? 'light' }}", "size": "default", "showuser": false}, "topbar": {"color": "dark"}, "showRightSidebarOnPageLoad": true}'
    @yield('body-extra')>
    <!-- Begin page -->
    <div id="wrapper">
        @auth
            @if (Auth::user()->status == 0)<!--用戶是否啟用-->
                @if (Auth::user()->job_id == 1 || Auth::user()->job_id == 7)
                    <!-- 老闆(1) 工程師(7)-->
                    @include('layouts.topbar/admin-topbar')
                @elseif(Auth::user()->job_id == 2)
                    <!-- 行政主管(2) -->
                    @include('layouts.topbar/admin-istrative-topbar')
                @elseif(Auth::user()->job_id == 3)
                    <!-- 專員主管(3) -->
                    @include('layouts.topbar/admin-sale-topbar')
                @elseif(Auth::user()->job_id == 4)
                    <!-- 行政(4) -->
                    @include('layouts.topbar/istrative-topbar')
                @elseif(Auth::user()->job_id == 5)
                    <!-- 專員(5) -->
                    @include('layouts.topbar/sale-topbar')
                @elseif(Auth::user()->job_id == 6)
                    <!-- 股東(6) -->
                    @include('layouts.topbar/shareholder-topbar')
                @elseif(Auth::user()->job_id == 9)
                    <!-- 純對帳帳號(9) -->
                    @include('layouts.topbar/check-topbar')
                @else
                    @include('layouts.topbar/sale-topbar')
                @endif
            @endif

            @if (Auth::user()->status == 0)<!--用戶是否啟用-->
                @include('layouts.shared/all-left-sidebar')
            {{-- @if (Auth::user()->job_id == 1 || Auth::user()->job_id == 7)
                <!-- 老闆(1) 工程師(7)-->
                @include('layouts.shared/admin-left-sidebar')
            @elseif(Auth::user()->job_id == 2)
                <!-- 行政主管(2) -->
                @include('layouts.shared/admin-istrative-left-sidebar')
            @elseif(Auth::user()->job_id == 3)
                <!-- 專員主管(3) -->
                @include('layouts.shared/admin-sale-left-sidebar')
            @elseif(Auth::user()->job_id == 4)
                <!-- 行政(4) -->
                @include('layouts.shared/istrative-left-sidebar')
            @elseif(Auth::user()->job_id == 5)
                <!-- 專員(5) -->
                @include('layouts.shared/sale-left-sidebar')
            @elseif(Auth::user()->job_id == 6)
                <!-- 股東(6) -->
                @include('layouts.shared/shareholder-left-sidebar')
            @elseif(Auth::user()->job_id == 9)
                <!-- 純對帳帳號(9) -->
                @include('layouts.shared/admin-check-left-sidebar')
            @elseif(Auth::user()->job_id == 10)
                <!-- 專員組長(10) -->
                @include('layouts.shared/leader-sale-left-sidebar')
            @else
                @include('layouts.shared/sale-left-sidebar')
            @endif --}}
            @endif
        @else
            <!-- 未登入時：重定向到登入頁面 -->
            <script>
                window.location.href = '{{ route('login') }}';
            </script>
        @endauth

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                @yield('content')
            </div>
            <!-- content -->

            {{-- @include('layouts.shared/footer') --}}

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
