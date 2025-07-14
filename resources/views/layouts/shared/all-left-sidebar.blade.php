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
            <ul id="side-menu" class="list-unstyled">

                {{-- 主要選單 (type = main) --}}
                @php $mainMenus = $sidebarMenus->where('parent_id', null)->where('type', 'main'); @endphp
                @if ($mainMenus->count() > 0)
                    <li class="menu-title">主要</li>
                    @foreach ($mainMenus as $parent)
                        @php
                            $children = $sidebarMenus->where('parent_id', $parent->id)->sortBy('sort');
                            $hasChildren = $children->count() > 0;
                        @endphp
                        <li>
                            <a href="{{ $hasChildren ? '#menu-' . $parent->id : route($parent->url) }}"
                                @if ($hasChildren) data-bs-toggle="collapse" @endif>
                                <i data-feather="{{ $parent->icon ?? 'circle' }}"></i>
                                <span>{{ $parent->name }}</span>
                                @if ($hasChildren)
                                    <span class="menu-arrow"></span>
                                @endif
                            </a>

                            @if ($hasChildren)
                                <div class="collapse" id="menu-{{ $parent->id }}">
                                    <ul class="nav-second-level list-unstyled">
                                        {{-- 第二層 --}}
                                        @foreach ($children as $child)
                                            @php
                                                $grands = $sidebarMenus->where('parent_id', $child->id)->sortBy('sort');
                                                $hasGrands = $grands->count() > 0;
                                            @endphp
                                            <li>
                                                <a href="{{ $hasGrands ? '#menu-' . $child->id : route($child->url) }}"
                                                    @if ($hasGrands) data-bs-toggle="collapse" @endif>
                                                    <span>{{ $child->name }}</span>
                                                    @if ($hasGrands)
                                                        <span class="menu-arrow"></span>
                                                    @endif
                                                </a>
                                                @if ($hasGrands)
                                                    <div class="collapse" id="menu-{{ $child->id }}">
                                                        <ul class="nav-second-level list-unstyled">
                                                            {{-- 第三層 --}}
                                                            @foreach ($grands as $grand)
                                                                <li>
                                                                    <a href="{{ route($grand->url) }}">
                                                                        {{ $grand->name }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        </li>
                    @endforeach
                @endif

                {{-- Apps 選單 (type = app) --}}
                @php $appMenus = $sidebarMenus->where('parent_id', null)->where('type', 'app'); @endphp
                @if ($appMenus->count() > 0)
                    <li class="menu-title mt-2">Apps</li>
                    @foreach ($appMenus as $parent)
                        @php
                            $children = $sidebarMenus->where('parent_id', $parent->id)->sortBy('sort');
                            $hasChildren = $children->count() > 0;
                        @endphp
                        <li>
                            <a href="{{ $hasChildren ? '#menu-' . $parent->id : route($parent->url) }}"
                                @if ($hasChildren) data-bs-toggle="collapse" @endif>
                                <i data-feather="{{ $parent->icon ?? 'circle' }}"></i>
                                <span>{{ $parent->name }}</span>
                                @if ($hasChildren)
                                    <span class="menu-arrow"></span>
                                @endif
                            </a>

                            @if ($hasChildren)
                                <div class="collapse" id="menu-{{ $parent->id }}">
                                    <ul class="nav-second-level list-unstyled">
                                        {{-- 第二層 --}}
                                        @foreach ($children as $child)
                                            @php
                                                $grands = $sidebarMenus->where('parent_id', $child->id)->sortBy('sort');
                                                $hasGrands = $grands->count() > 0;
                                            @endphp
                                            <li>
                                                <a href="{{ $hasGrands ? '#menu-' . $child->id : route($child->url) }}"
                                                    @if ($hasGrands) data-bs-toggle="collapse" @endif>
                                                    <span>{{ $child->name }}</span>
                                                    @if ($hasGrands)
                                                        <span class="menu-arrow"></span>
                                                    @endif
                                                </a>
                                                @if ($hasGrands)
                                                    <div class="collapse" id="menu-{{ $child->id }}">
                                                        <ul class="nav-second-level list-unstyled">
                                                            {{-- 第三層 --}}
                                                            @foreach ($grands as $grand)
                                                                <li>
                                                                    <a href="{{ route($grand->url) }}">
                                                                        {{ $grand->name }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        </li>
                    @endforeach
                @endif

            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->
