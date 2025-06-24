<!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu">

    <div class="h-100" data-simplebar>

        <!-- User box -->
        <div class="user-box text-center">
            <img src="{{asset('assets/images/users/user-9.jpg')}}" alt="user-img" title="Mat Helme" class="rounded-circle avatar-md">
            <div class="dropdown">
                <a href="javascript: void(0);" class="text-dark dropdown-toggle h5 mt-2 mb-1 d-block" data-bs-toggle="dropdown">James Kennedy</a>
                <div class="dropdown-menu user-pro-dropdown">

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-user me-1"></i>
                        <span>個人資訊</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-settings me-1"></i>
                        <span>變更密碼</span>
                    </a>

                    <!-- item-->
                    {{-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-lock me-1"></i>
                        <span>Lock Screen</span>
                    </a> --}}

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-log-out me-1"></i>
                        <span>Logout</span>
                    </a>

                </div>
            </div>
            <p class="text-muted">Admin Head</p>
        </div>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul id="side-menu">

                <li class="menu-title">主要</li>

                <li>
                    <a href="{{ route('index') }}">
                        <i data-feather="airplay"></i>
                        <span> 資訊總覽 </span>
                    </a>
                </li>
                <li class="menu-title mt-2">Apps</li>

                <li>
                    <li>
                        <a href="#visit" data-bs-toggle="collapse">
                            <i data-feather="github"></i>
                            <span> 拜訪管理 </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="visit">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{route('hospitals')}}"  class="{{ request()->is('hospitals') ? 'active' : '' }}">醫院紀錄</a>
                                </li>
                                <li>
                                    <a href="{{route('etiquettes')}}"  class="{{ request()->is('etiquettes') ? 'active' : '' }}">禮儀社紀錄</a>
                                </li>
                                <li>
                                    <a href="{{route('reproduces')}}"  class="{{ request()->is('reproduces') ? 'active' : '' }}">繁殖場紀錄</a>
                                </li>
                                <li>
                                    <a href="{{route('dogparks')}}"  class="{{ request()->is('dogparks') ? 'active' : '' }}">狗園紀錄</a>
                                </li>
                                <li>
                                    <a href="{{route('salons')}}"  class="{{ request()->is('salons') ? 'active' : '' }}">美容院紀錄</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="#customer" data-bs-toggle="collapse">
                            <i data-feather="life-buoy"></i>
                            <span> 客戶管理 </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="customer">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{ route('customer.create')}}"  class="{{ request()->is('customer.create') ? 'active' : '' }}">新增客戶</a>
                                </li>
                                <li>
                                    <a href="{{ route('customer')}}"  class="{{ request()->is('customer') ? 'active' : '' }}">客戶資料</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="#sale" data-bs-toggle="collapse">
                            <i data-feather="codesandbox"></i>
                            <span> 業務管理 </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sale">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{ route('person.sales') }}"  class="{{ request()->is('person.sales') ? 'active' : '' }}">業務管理</a>
                                </li>
                                <li>
                                    <a href="{{ route('sale.create') }}"  class="{{ request()->is('sale.create') ? 'active' : '' }}">業務Key單</a>
                                </li>
                                <li>
                                    <a href="{{ route('person.wait.sales') }}"  class="{{ request()->is('person.wait.sales') ? 'active' : '' }}">業務對帳確認</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="#puja_data" data-bs-toggle="collapse">
                            <i data-feather="codesandbox"></i>
                            <span> 法會報名管理 </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="puja_data">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{ route('puja_data.create') }}"  class="{{ request()->is('puja_data.create') ? 'active' : '' }}">法會報名</a>
                                </li>
                                <li>
                                    <a href="{{ route('puja_datas') }}"  class="{{ request()->is('puja_datas') ? 'active' : '' }}">法會報名查詢</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="#pay" data-bs-toggle="collapse">
                            <i data-feather="trending-down"></i>
                            <span> 支出管理 </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="pay">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{ route('pays') }}"  class="{{ request()->is('pays') ? 'active' : '' }}">支出管理</a>
                                </li>
                                <li>
                                    <a href="{{ route('pay.create') }}"  class="{{ request()->is('pay.create') ? 'active' : '' }}">支出Key單</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="#leave_day" data-bs-toggle="collapse">
                            <i data-feather="trending-down"></i>
                            <span> 請假管理 </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="leave_day">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{ route('person.leave_days') }}"  class="{{ request()->is('person.leave_days') ? 'active' : '' }}">請假總覽</a>
                                </li>
                                <li>
                                    <a href="{{ route('leave_day.create') }}"  class="{{ request()->is('leave_day.create') ? 'active' : '' }}">請假申請</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </li>
                <li class="menu-title mt-2">fast Use</li>
                <li>
                    <a href="{{ route('sale.create') }}">
                        <i data-feather="airplay"></i>
                        <span> 業務key單 </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pay.create') }}">
                        <i data-feather="airplay"></i>
                        <span> 支出key單 </span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i data-feather="airplay"></i>
                        <span> 請假key單 </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('person.inventory') }}">
                        <i data-feather="airplay"></i>
                        <span> 商品盤點 </span>
                    </a>
                </li>
                
                
            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->