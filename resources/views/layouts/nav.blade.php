<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element"> <span>
                        <img alt="image" class="cimg" src="{{ asset('assets/images/logo-icon.png') }}"
                            style="width:34px;margin-top: -10px;" /> <span style="font-size:24px; ">Fitness</span>
                    </span>

                </div>
                <div class="logo-element">
                    <img class="" src="{{ asset('assets/images/logo-icon.pn') }}'" style="width:34px">
                </div>
            </li>
            <li class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard')}}"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span>
                </a>

            </li>
            <li class="{{ Request::is('admin/users') ? 'active' : '' }}">
                <a href="{{ route('admin.users')}}"><i class="fa fa-user-plus"></i> <span class="nav-label">Users
                    </span></a>
            </li>
            <li class="">
                <a href="gym-management.html"><i class="fa fa-building"></i> <span class="nav-label">Gym
                        Management
                    </span></a>
            </li>
            <li class="">
                <a href=""><i class="fa fa-file"></i> <span class="nav-label">Booking Management
                    </span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li class=""><a href="user-wise.html">User Wise</a></li>
                    <li><a href="gym-wise.html">Gym Wise</a></li>
                </ul>
            </li>
            <li class="">
                <a href="report.html"><i class="fa fa-file-text"></i> <span class="nav-label">Report
                    </span></a>
            </li>
            <li>
                <a href="customer-support.html"><i class="fa fa-headphones"></i> <span class="nav-label">Customer
                        Support
                    </span></a>
            </li>
            <li>
                <a href="community.html"><i class="fa fa-users"></i> <span class="nav-label">Community
                    </span></a>
            </li>
            <li>
                <a href="logout.html"><i class="fa fa-sign-out"></i> <span class="nav-label">Log Out</span></a>
            </li>


        </ul>

    </div>
</nav>
