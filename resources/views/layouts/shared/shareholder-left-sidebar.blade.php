<!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu">

    <div class="h-100" data-simplebar>

        <!-- User box -->
        <div class="user-box text-center">
            <img src="{{ asset('assets/images/users/user-9.jpg') }}" alt="user-img" title="Mat Helme"
                class="rounded-circle avatar-md">
            <div class="dropdown">
                <a href="javascript: void(0);" class="text-dark dropdown-toggle h5 mt-2 mb-1 d-block"
                    data-bs-toggle="dropdown">James Kennedy</a>
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
                        <span> 資訊總覽 </span>
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
                                            <a href="{{ route('rpg23') }}"
                                                class="{{ request()->is('rpg23') ? 'active' : '' }}">客戶分佈報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg24') }}"
                                                class="{{ request()->is('rpg24') ? 'active' : '' }}">每月客戶新增數量</a>
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
                                            <a href="{{ route('rpg14') }}"
                                                class="{{ request()->is('rpg14') ? 'active' : '' }}">每月來源報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg27') }}"
                                                class="{{ request()->is('rpg27') ? 'active' : '' }}">年度來源</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg01') }}"
                                                class="{{ request()->is('rpg01') ? 'active' : '' }}">每月方案報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg17') }}"
                                                class="{{ request()->is('rpg17') ? 'active' : '' }}">年度安葬服務</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg16') }}"
                                                class="{{ request()->is('rpg16') ? 'active' : '' }}">年度後續服務</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg25') }}"
                                                class="{{ request()->is('rpg25') ? 'active' : '' }}">年度其他服務</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg04') }}"
                                                class="{{ request()->is('rpg04') ? 'active' : '' }}">每月金紙銷售報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg13') }}"
                                                class="{{ request()->is('rpg13') ? 'active' : '' }}">每月金紙報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg22') }}"
                                                class="{{ request()->is('rpg22') ? 'active' : '' }}">年度紀念品</a>
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
                                            <a href="{{ route('rpg02') }}"
                                                class="{{ request()->is('rpg02') ? 'active' : '' }}">支出報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg20') }}"
                                                class="{{ request()->is('rpg20') ? 'active' : '' }}">支出比較報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg05') }}"
                                                class="{{ request()->is('rpg05') ? 'active' : '' }}">日營收報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg09') }}"
                                                class="{{ request()->is('rpg09') ? 'active' : '' }}">每月營收報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg11') }}"
                                                class="{{ request()->is('rpg11') ? 'active' : '' }}">年度營收報表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg26') }}"
                                                class="{{ request()->is('rpg26') ? 'active' : '' }}">營收總表</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg18') }}"
                                                class="{{ request()->is('rpg18') ? 'active' : '' }}">法會收入統計</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg29') }}"
                                                class="{{ request()->is('rpg29') ? 'active' : '' }}">合約收入統計</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg28') }}"
                                                class="{{ request()->is('rpg28') ? 'active' : '' }}">平安燈收入統計</a>
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
                                            <a href="{{ route('rpg15') }}"
                                                class="{{ request()->is('rpg15') ? 'active' : '' }}">專員各單量統計</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg21') }}"
                                                class="{{ request()->is('rpg21') ? 'active' : '' }}">專員年度業務金額統計</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg10') }}"
                                                class="{{ request()->is('rpg10') ? 'active' : '' }}">專員金紙獎金</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li>
                                <a href="#sidebarMultilevel6" data-bs-toggle="collapse">
                                    其他報表 <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarMultilevel6">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a href="{{ route('rpg12') }}"
                                                class="{{ request()->is('rpg12') ? 'active' : '' }}">廠商佣金抽成</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg07') }}"
                                                class="{{ request()->is('rpg07') ? 'active' : '' }}">團火查詢</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('rpg06') }}"
                                                class="{{ request()->is('rpg06') ? 'active' : '' }}">套組法會查詢</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->
