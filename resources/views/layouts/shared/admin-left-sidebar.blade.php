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
                <li>
                    <a href="{{ route('task') }}">
                        <i data-feather="message-circle"></i>
                        <span> 待辦管理 </span>
                    </a>
                </li>



                <li class="menu-title mt-2">Apps</li>

                <li>
                    <a href="#customer" data-bs-toggle="collapse">
                        <i data-feather="life-buoy"></i>
                        <span> 客戶管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="customer">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('customer.create') }}"
                                    class="{{ request()->is('customer.create') ? 'active' : '' }}">新增客戶</a>
                            </li>

                            <li>
                                <a href="{{ route('customer') }}"
                                    class="{{ request()->is('customer') ? 'active' : '' }}">客戶資料</a>
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
                                <a href="{{ route('sales') }}"
                                    class="{{ request()->is('sales') ? 'active' : '' }}">業務管理</a>
                            </li>
                            <li>
                                <a href="{{ route('sale.create') }}"
                                    class="{{ request()->is('sale.create') ? 'active' : '' }}">業務Key單</a>
                            </li>
                            <li>
                                <a href="{{ route('wait.sales') }}"
                                    class="{{ request()->is('wait.sales') ? 'active' : '' }}">業務對帳確認</a>
                            </li>
                            <li>
                                <a href="{{ route('sales.checkHistory') }}"
                                    class="{{ request()->is('sales.checkHistory') ? 'active' : '' }}">業務對帳明細</a>
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
                                <a href="{{ route('puja_data.create') }}"
                                    class="{{ request()->is('puja_data.create') ? 'active' : '' }}">法會報名</a>
                            </li>
                            <li>
                                <a href="{{ route('puja_datas') }}"
                                    class="{{ request()->is('puja_datas') ? 'active' : '' }}">法會管理</a>
                            </li>
                            <li>
                                <a href="{{ route('puja.create') }}"
                                    class="{{ request()->is('puja.create') ? 'active' : '' }}">法會場次設定</a>
                            </li>
                            <li>
                                <a href="{{ route('pujas') }}"
                                    class="{{ request()->is('pujas') ? 'active' : '' }}">法會場次查詢</a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#visit" data-bs-toggle="collapse">
                        <i data-feather="github"></i>
                        <span> 拜訪管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="visit">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('hospitals') }}"
                                    class="{{ request()->is('hospitals') ? 'active' : '' }}">醫院紀錄</a>
                            </li>
                            <li>
                                <a href="{{ route('etiquettes') }}"
                                    class="{{ request()->is('etiquettes') ? 'active' : '' }}">禮儀社紀錄</a>
                            </li>
                            <li>
                                <a href="{{ route('reproduces') }}"
                                    class="{{ request()->is('reproduces') ? 'active' : '' }}">繁殖場紀錄</a>
                            </li>
                            <li>
                                <a href="{{ route('dogparks') }}"
                                    class="{{ request()->is('dogparks') ? 'active' : '' }}">狗園紀錄</a>
                            </li>
                            <li>
                                <a href="{{ route('salons') }}"
                                    class="{{ request()->is('salons') ? 'active' : '' }}">美容院紀錄</a>
                            </li>
                            <li>
                                <a href="{{ route('others') }}"
                                    class="{{ request()->is('others') ? 'active' : '' }}">其他業者紀錄</a>
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
                                <a href="{{ route('contracts') }}"
                                    class="{{ request()->is('contracts') ? 'active' : '' }}">合約管理</a>
                            </li>
                            <li>
                                <a href="{{ route('contract.create') }}"
                                    class="{{ request()->is('contract.create') ? 'active' : '' }}">新增合約</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#lamp" data-bs-toggle="collapse">
                        <i data-feather="smile"></i>
                        <span> 平安燈管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="lamp">
                        <ul class="nav-second-level">

                            <li>
                                <a href="{{ route('lamps') }}"
                                    class="{{ request()->is('lamps') ? 'active' : '' }}">平安燈管理</a>
                            </li>
                            <li>
                                <a href="{{ route('lamp.create') }}"
                                    class="{{ request()->is('lamp.create') ? 'active' : '' }}">新增平安燈</a>
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
                                <a href="{{ route('pays') }}"
                                    class="{{ request()->is('pays') ? 'active' : '' }}">支出管理</a>
                            </li>
                            <li>
                                <a href="{{ route('pay.create') }}"
                                    class="{{ request()->is('pay.create') ? 'active' : '' }}">支出Key單</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#income" data-bs-toggle="collapse">
                        <i data-feather="trending-up"></i>
                        <span> 收入管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="income">
                        <ul class="nav-second-level">

                            <li>
                                <a href="{{ route('incomes') }}"
                                    class="{{ request()->is('incomes') ? 'active' : '' }}">收入管理</a>
                            </li>
                            <li>
                                <a href="{{ route('income.create') }}"
                                    class="{{ request()->is('income.create') ? 'active' : '' }}">收入key單</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#deregistration" data-bs-toggle="collapse">
                        <i data-feather="slack"></i>
                        <span> 除戶管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="deregistration">
                        <ul class="nav-second-level">

                            <li>
                                <a href="{{ route('deregistration.index') }}"
                                    class="{{ request()->is('deregistration.index') ? 'active' : '' }}">除戶管理</a>
                            </li>
                            <li>
                                <a href="{{ route('deregistration.create') }}"
                                    class="{{ request()->is('deregistration.create') ? 'active' : '' }}">新增除戶</a>
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
                                <a href="{{ route('cashs') }}"
                                    class="{{ request()->is('cashs') ? 'active' : '' }}">零用金管理</a>
                            </li>
                            <li>
                                <a href="{{ route('cash.create') }}"
                                    class="{{ request()->is('cash.create') ? 'active' : '' }}">零用金Key單</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#holiday" data-bs-toggle="collapse">
                        <i data-feather="users"></i>
                        <span> 人事管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="holiday">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('personnel.leaves') }}"
                                    class="{{ request()->is('personnel.leaves') ? 'active' : '' }}">假別列表</a>
                            </li>
                            <li>
                                <a href="{{ route('personnels') }}"
                                    class="{{ request()->is('personnels') ? 'active' : '' }}">人事列表</a>
                            </li>
                            <li>
                                <a href="{{ route('personnel.leave_days') }}"
                                    class="{{ request()->is('personnel.leave_days') ? 'active' : '' }}">請假核准</a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('vacations') }}"  class="{{ request()->is('vacations') ? 'active' : '' }}">每月總休假設定</a>
                            </li> --}}
                            <li>
                                <a href="{{ route('personnel.holidays') }}"
                                    class="{{ request()->is('personnel.holidays') ? 'active' : '' }}">例休假總覽</a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('personnel.other_holidays') }}"  class="{{ request()->is('personnel.other_holidays') ? 'active' : '' }}">特休總覽</a>
                            </li> --}}
                            <li>
                                <a href="{{ route('personnel.other_holidays') }}"
                                    class="{{ request()->is('personnel.other_holidays') ? 'active' : '' }}">其他假總覽</a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('user.bank') }}"  class="{{ request()->is('user.bank') ? 'active' : '' }}">專員戶頭設定</a>
                            </li> --}}
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#product" data-bs-toggle="collapse">
                        <i data-feather="shopping-cart"></i>
                        <span> 商品管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="product">
                        <ul class="nav-second-level">

                            <li>
                                <a href="{{ route('product.create') }}"
                                    class="{{ request()->is('product.create') ? 'active' : '' }}">新增商品</a>
                            </li>
                            <li>
                                <a href="{{ route('product') }}"
                                    class="{{ request()->is('product') ? 'active' : '' }}">商品列表</a>
                            </li>
                            <li>
                                <a href="{{ route('product.restock') }}"
                                    class="{{ request()->is('product.restock') ? 'active' : '' }}">商品進貨</a>
                            </li>
                            <li>
                                <a href="{{ route('product.inventorys') }}"
                                    class="{{ request()->is('product.inventorys') ? 'active' : '' }}">庫存盤點</a>
                            </li>
                        </ul>
                    </div>
                </li>



                <li>
                    <a href="#other" data-bs-toggle="collapse">
                        <i data-feather="database"></i>
                        <span> 設定管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="other">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('customer.group') }}"
                                    class="{{ request()->is('customer.group') ? 'active' : '' }}">客戶群組</a>
                            </li>
                            <li>
                                <a href="{{ route('contractTypes') }}"
                                    class="{{ request()->is('contractTypes') ? 'active' : '' }}">合約類別</a>
                            </li>
                            <li>
                                <a href="{{ route('lampTypes') }}"
                                    class="{{ request()->is('lampTypes') ? 'active' : '' }}">平安燈類別</a>
                            </li>
                            <li>
                                <a href="{{ route('puja.types') }}"
                                    class="{{ request()->is('puja.types') ? 'active' : '' }}">法會類別</a>
                            </li>

                            <li>
                                <a href="{{ route('pay.sujects') }}"
                                    class="{{ request()->is('pay.sujects') ? 'active' : '' }}">支出科目設定</a>
                            </li>
                            <li>
                                <a href="{{ route('income.sujects') }}"
                                    class="{{ request()->is('income.sujects') ? 'active' : '' }}">收入科目設定</a>
                            </li>
                            <li>
                                <a href="{{ route('product.category') }}"
                                    class="{{ request()->is('product.category') ? 'active' : '' }}">商品類別</a>
                            </li>
                            <li>
                                <a href="{{ route('sources') }}"
                                    class="{{ request()->is('sources') ? 'active' : '' }}">來源設定</a>
                            </li>
                            <li>
                                <a href="{{ route('plans') }}"
                                    class="{{ request()->is('plans') ? 'active' : '' }}">方案設定</a>
                            </li>
                            <li>
                                <a href="{{ route('suits') }}"
                                    class="{{ request()->is('suits') ? 'active' : '' }}">套裝設定</a>
                            </li>
                            <li>
                                <a href="{{ route('targetCategories') }}"
                                    class="{{ request()->is('targetCategories') ? 'active' : '' }}">達標類別設定</a>
                            </li>
                            <li>
                                <a href="{{ route('prom_types') }}"
                                    class="{{ request()->is('prom_types') ? 'active' : '' }}">後續處理類別</a>
                            </li>
                            <li>
                                <a href="{{ route('proms') }}"
                                    class="{{ request()->is('proms') ? 'active' : '' }}">後續處理細項</a>
                            </li>
                            <li>
                                <a href="{{ route('souvenir_types') }}"
                                    class="{{ request()->is('souvenir_types') ? 'active' : '' }}">紀念品類別</a>
                            </li>
                            <li>
                                <a href="{{ route('souvenirs') }}"
                                    class="{{ request()->is('souvenirs') ? 'active' : '' }}">紀念品細項</a>
                            </li>
                            <li>
                                <a href="{{ route('venders') }}"
                                    class="{{ request()->is('venders') ? 'active' : '' }}">廠商資料</a>
                            </li>
                            <li>
                                <a href="{{ route('menu.index') }}"
                                    class="{{ request()->is('menu.index') ? 'active' : '' }}">選單設定</a>
                            </li>
                            <li>
                                <a href="{{ route('job.menu.index') }}"
                                    class="{{ request()->is('job.menu.index') ? 'active' : '' }}">權限選單</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#sidebarCustomer" data-bs-toggle="collapse">
                        <i data-feather="file-text"></i>
                        <span> 報表管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarCustomer">
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
                                        <li>
                                            <a href="{{ route('rpg31') }}"
                                                class="{{ request()->is('rpg31') ? 'active' : '' }}">年度平安燈</a>
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
                                        <li>
                                            <a href="{{ route('rpg30') }}"
                                                class="{{ request()->is('rpg30') ? 'active' : '' }}">月/季獎金統計</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#tatget" data-bs-toggle="collapse">
                        <i data-feather="bar-chart"></i>
                        <span> 達標管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="tatget">
                        <ul class="nav-second-level">

                            <li>
                                <a href="{{ route('target.create') }}"
                                    class="{{ request()->is('target.create') ? 'active' : '' }}">新增達標</a>
                            </li>
                            <li>
                                <a href="{{ route('target') }}"
                                    class="{{ request()->is('target') ? 'active' : '' }}">達標列表</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                <li>
                    <a href="#sidebarEcommerce" data-bs-toggle="collapse">
                        <i data-feather="users"></i>
                        <span> 用戶管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarEcommerce">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('branchs') }}"
                                    class="{{ request()->is('branchs') ? 'active' : '' }}">部門列表</a>
                            </li>
                            <li>
                                <a href="{{ route('jobs') }}"
                                    class="{{ request()->is('jobs') ? 'active' : '' }}">職稱列表</a>
                            </li>
                            <li>
                                <a href="{{ route('user.create') }}"
                                    class="{{ request()->is('user.create') ? 'active' : '' }}">新增用戶</a>
                            </li>
                            <li>
                                <a href="{{ route('users') }}"
                                    class="{{ request()->is('users') ? 'active' : '' }}">用戶列表</a>
                            </li>
                        </ul>
                    </div>
                </li>
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
                                <a href="{{ route('person.last_leave_days') }}"
                                    class="{{ request()->is('person.last_leave_days') ? 'active' : '' }}">剩餘假總覽</a>
                            </li>
                            <li>
                                <a href="{{ route('person.pays') }}"
                                    class="{{ request()->is('person.pays') ? 'active' : '' }}">個人支出</a>
                            </li>
                            <li>
                                <a href="{{ route('person.leave_days') }}"
                                    class="{{ request()->is('person.leave_days') ? 'active' : '' }}">個人假單</a>
                            </li>
                            <li>
                                <a href="{{ route('user-profile') }}"
                                    class="{{ request()->is('user-profile') ? 'active' : '' }}">個人資料</a>
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
