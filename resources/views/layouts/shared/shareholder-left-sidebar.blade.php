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
                <li>
                    <a href="{{ route('dashboard.info') }}">
                        <i data-feather="airplay"></i>
                        <span> 當月總表 </span>
                    </a>
                </li>

                
                <li class="menu-title mt-2">Apps</li>
                <li>
                    <a href="#sale" data-bs-toggle="collapse">
                        <i data-feather="codesandbox"></i>
                        <span> 業務管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sale">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('wait.sales') }}"  class="{{ request()->is('wait.sales') ? 'active' : '' }}">業務對帳確認</a>
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
                                <a href="{{ route('pay.sujects') }}"  class="{{ request()->is('pay.sujects') ? 'active' : '' }}">支出科目</a>
                            </li>
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
                    <a href="#sidebarMultilevel" data-bs-toggle="collapse">
                        <i data-feather="file-text"></i>
                        <span> 報表管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarMultilevel">
                        <ul class="nav-second-level">
                            <li>
                                <a href="#sidebarCustomer2" data-bs-toggle="collapse">
                                    客戶報表 <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarCustomer2">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ route('rpg23') }}"  class="{{ request()->is('rpg23') ? 'active' : '' }}">客戶分佈報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg24') }}"  class="{{ request()->is('rpg24') ? 'active' : '' }}">每月客戶新增數量</a>
                                        </li>
                                        
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#sidebarMultilevel2" data-bs-toggle="collapse">
                                    銷售報表 <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarMultilevel2">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ route('rpg14') }}"  class="{{ request()->is('rpg14') ? 'active' : '' }}">來源報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg01') }}"  class="{{ request()->is('rpg01') ? 'active' : '' }}">方案報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg17') }}"  class="{{ request()->is('rpg17') ? 'active' : '' }}">安葬服務報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg16') }}"  class="{{ request()->is('rpg16') ? 'active' : '' }}">後續服務報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg04') }}"  class="{{ request()->is('rpg04') ? 'active' : '' }}">金紙銷售報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg13') }}"  class="{{ request()->is('rpg13') ? 'active' : '' }}">金紙賣出報表</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li>
                                <a href="#sidebarMultilevel3" data-bs-toggle="collapse">
                                    收支報表 <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarMultilevel3">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ route('rpg02') }}"  class="{{ request()->is('rpg02') ? 'active' : '' }}">支出報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg20') }}"  class="{{ request()->is('rpg20') ? 'active' : '' }}">支出比較報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg05') }}"  class="{{ request()->is('rpg05') ? 'active' : '' }}">日營收報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg09') }}"  class="{{ request()->is('rpg09') ? 'active' : '' }}">每月營收報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg11') }}"  class="{{ request()->is('rpg11') ? 'active' : '' }}">年度營收報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg18') }}"  class="{{ request()->is('rpg18') ? 'active' : '' }}">法會收入統計</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li>
                                <a href="#sidebarMultilevel4" data-bs-toggle="collapse">
                                    獎金報表 <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarMultilevel4">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ route('rpg22') }}"  class="{{ request()->is('rpg22') ? 'active' : '' }}">每月紀念品銷售統計</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg12') }}"  class="{{ request()->is('rpg12') ? 'active' : '' }}">廠商佣金抽成</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li>
                                <a href="#sidebarMultilevel5" data-bs-toggle="collapse">
                                    專員報表 <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarMultilevel5">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ route('rpg15') }}"  class="{{ request()->is('rpg15') ? 'active' : '' }}">專員各單量統計</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg21') }}"  class="{{ request()->is('rpg21') ? 'active' : '' }}">專員每月業務金額統計</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg10') }}"  class="{{ request()->is('rpg10') ? 'active' : '' }}">專員金紙獎金</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li>
                                <a href="#sidebarMultilevel6" data-bs-toggle="collapse">
                                    未分類報表 <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarMultilevel6">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ route('rpg07') }}"  class="{{ request()->is('rpg07') ? 'active' : '' }}">團火查詢</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg06') }}"  class="{{ request()->is('rpg06') ? 'active' : '' }}">套組法會查詢</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>


            </li>

                
            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->