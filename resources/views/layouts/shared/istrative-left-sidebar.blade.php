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
                        <i data-feather="home"></i>
                        <span> 線上打卡 </span>
                    </a>
                </li>
                {{-- <li>
                    <a href="{{ route('dashboard.info') }}">
                        <i data-feather="airplay"></i>
                        <span> 當月總表 </span>
                    </a>
                </li> --}}

                

                <li class="menu-title mt-2">Apps</li>

                {{-- <li>
                    <li>
                        <a href="#sidebarEcommerce" data-bs-toggle="collapse">
                            <i data-feather="users"></i>
                            <span> 用戶管理 </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarEcommerce">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{route('user.create')}}"  class="{{ request()->is('user.create') ? 'active' : '' }}">新增用戶</a>
                                </li>
                                <li>
                                    <a href="{{route('users')}}"  class="{{ request()->is('users') ? 'active' : '' }}">用戶列表</a>
                                </li>
                                <li>
                                    <a href="{{route('branchs')}}"  class="{{ request()->is('branchs') ? 'active' : '' }}">部門列表</a>
                                </li>
                                <li>
                                    <a href="{{route('jobs')}}"  class="{{ request()->is('jobs') ? 'active' : '' }}">職稱列表</a>
                                </li>
                            </ul>
                        </div>
                    </li> --}}

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
                                    <a href="{{ route('customer')}}"  class="{{ request()->is('customer') ? 'active' : '' }}">客戶列表</a>
                                </li>
                                <li>
                                    <a href="{{ route('customer.group') }}"  class="{{ request()->is('customer.group') ? 'active' : '' }}">客戶群組</a>
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
                                    <a href="{{ route('sales') }}"  class="{{ request()->is('sales') ? 'active' : '' }}">業務管理</a>
                                </li>
                                <li>
                                    <a href="{{ route('sale.create') }}"  class="{{ request()->is('sale.create') ? 'active' : '' }}">業務Key單</a>
                                </li>
                                <li>
                                    <a href="{{ route('wait.sales') }}"  class="{{ request()->is('wait.sales') ? 'active' : '' }}">業務對帳確認</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="#puja" data-bs-toggle="collapse">
                            <i data-feather="feather"></i>
                            <span> 法會管理 </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="puja">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{ route('pujas') }}"  class="{{ request()->is('pujas') ? 'active' : '' }}">法會管理</a>
                                </li>
                                <li>
                                    <a href="{{ route('puja.create') }}"  class="{{ request()->is('puja.create') ? 'active' : '' }}">法會設定</a>
                                </li>
                                <li>
                                    <a href="{{ route('puja.types') }}"  class="{{ request()->is('puja.types') ? 'active' : '' }}">法會類別設定</a>
                                </li>
                                <li>
                                    <a href="{{ route('puja_datas') }}"  class="{{ request()->is('puja_datas') ? 'active' : '' }}">法會報名查詢</a>
                                </li>
                                <li>
                                    <a href="{{ route('puja_data.create') }}"  class="{{ request()->is('puja_data.create') ? 'active' : '' }}">法會報名</a>
                                </li>
                                
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="#contract" data-bs-toggle="collapse">
                            <i data-feather="folder"></i>
                            <span> 合約管理 </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="contract">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{ route('contractTypes') }}"  class="{{ request()->is('contractTypes') ? 'active' : '' }}">合約類別</a>
                                </li>
                                <li>
                                    <a href="{{ route('contracts') }}"  class="{{ request()->is('contracts') ? 'active' : '' }}">合約管理</a>
                                </li>
                                <li>
                                    <a href="{{ route('contract.create') }}"  class="{{ request()->is('contract.create') ? 'active' : '' }}">新增合約</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="#cash" data-bs-toggle="collapse">
                            <i data-feather="dollar-sign"></i>
                            <span> 零用金管理 </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="cash">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{ route('cashs') }}"  class="{{ request()->is('cashs') ? 'active' : '' }}">零用金管理</a>
                                </li>
                                <li>
                                    <a href="{{ route('cash.create') }}"  class="{{ request()->is('cash.create') ? 'active' : '' }}">零用金Key單</a>
                                </li>
                            </ul>
                        </div>
                    </li>


                <li>
                    <a href="#person" data-bs-toggle="collapse">
                        <i data-feather="user"></i>
                        <span> 個人管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="person">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('person.pays') }}"  class="{{ request()->is('person.pays') ? 'active' : '' }}">個人支出總覽</a>
                            </li>
                            <li>
                                <a href="{{ route('person.leave_days') }}"  class="{{ request()->is('person.leave_days') ? 'active' : '' }}">個人假單總覽</a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- <li class="menu-title mt-2">fast Use</li>
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
                </li> --}}
                
                
            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->