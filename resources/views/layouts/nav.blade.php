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
                <a href="{{ route('admin.users')}}"><i class="fa fa-user-plus"></i> <span class="nav-label">Users</span></a>
            </li>
            <li class="{{ Request::is('admin/classes*') ? 'active' : '' }}">
                <a href="{{ route('admin.classes.index') }}"><i class="fa fa-calendar"></i> <span class="nav-label">Sessions</span></a>
            </li>
            <li class="{{ Request::is('admin/exercise-categories*') || Request::is('admin/exercises*') || Request::is('admin/workout-plans*') || Request::is('admin/workout-logs*') || Request::is('admin/exercise-logs*') ? 'active' : '' }}">
                <a href=""><i class="fa fa-heartbeat"></i> <span class="nav-label">Workouts & Exercises</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse {{ Request::is('admin/exercise-categories*') || Request::is('admin/exercises*') || Request::is('admin/workout-plans*') || Request::is('admin/workout-logs*') || Request::is('admin/exercise-logs*') ? 'in' : '' }}">
                    <li class="{{ Request::is('admin/exercise-categories*') ? 'active' : '' }}"><a href="{{ route('admin.exercise-categories.index') }}">Exercise Categories</a></li>
                    <li class="{{ Request::is('admin/exercises*') ? 'active' : '' }}"><a href="{{ route('admin.exercises.index') }}">Exercises</a></li>
                    <li class="{{ Request::is('admin/workout-plans*') ? 'active' : '' }}"><a href="{{ route('admin.workout-plans.index') }}">Workout Plans</a></li>
                    <li class="{{ Request::is('admin/workout-logs*') ? 'active' : '' }}"><a href="{{ route('admin.workout-logs.index') }}">Workout Logs</a></li>
                    <li class="{{ Request::is('admin/exercise-logs*') ? 'active' : '' }}"><a href="{{ route('admin.exercise-logs.index') }}">Exercise Logs</a></li>
                </ul>
            </li>
            <li class="{{ Request::is('admin/nutrition/*') ? 'active' : '' }}">
                <a href=""><i class="fa fa-cutlery"></i> <span class="nav-label">Nutrition</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse {{ Request::is('admin/nutrition/*') ? 'in' : '' }}">
                    <li class="{{ Request::is('admin/nutrition/meals*') ? 'active' : '' }}"><a href="{{ route('admin.nutrition.meals.index') }}">Meals</a></li>
                    <li class="{{ Request::is('admin/nutrition/targets*') ? 'active' : '' }}"><a href="{{ route('admin.nutrition.targets.index') }}">Targets</a></li>
                    <li class="{{ Request::is('admin/nutrition/adherence*') ? 'active' : '' }}"><a href="{{ route('admin.nutrition.adherence.index') }}">Adherence</a></li>
                </ul>
            </li>
            <li class="{{ Request::is('admin/bookings*') ? 'active' : '' }}">
                <a href="{{ route('admin.bookings.index') }}"><i class="fa fa-ticket"></i> <span class="nav-label">Bookings</span></a>
            </li>
            <li class="{{ Request::is('admin/payments*') ? 'active' : '' }}">
                <a href="{{ route('admin.payments.index') }}"><i class="fa fa-credit-card"></i> <span class="nav-label">Payments</span></a>
            </li>
            <li class="{{ Request::is('admin/posts*') ? 'active' : '' }}">
                <a href="{{ route('admin.posts.index') }}"><i class="fa fa-comments"></i> <span class="nav-label">Social/Content (Posts)</span></a>
            </li>
            <li class="{{ Request::is('admin/users/gyms') ? 'active' : '' }}">
                <a href="{{ route('admin.users.gyms') }}"><i class="fa fa-building"></i> <span class="nav-label">Gym Management</span></a>
            </li>
            <li class="{{ Request::is('admin/users/trainers') ? 'active' : '' }}">
                <a href="{{ route('admin.users.trainers') }}"><i class="fa fa-user-secret"></i> <span class="nav-label">Trainer Management</span></a>
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
                <a onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i> <span class="nav-label">Log Out</span></a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>


        </ul>

    </div>
</nav>
