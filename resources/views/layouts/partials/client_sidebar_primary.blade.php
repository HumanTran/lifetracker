<li class="menu-item {{ Request::is('overview*') ? 'active' : '' }}">
    <a href="{{ route('overview.index') }}"><i class="fa-solid fa-chart-pie"></i><span>Tổng quan</span></a>
</li>

<li class="menu-item {{ Request::is('tasks*') ? 'active' : '' }}">
    <a href="{{ route('tasks.index') }}"><i class="fa-solid fa-check-square"></i><span>Công việc</span></a>
</li>

<li class="menu-item {{ Request::is('habits*') ? 'active' : '' }}">
    <a href="{{ route('habits.index') }}"><i class="fa-solid fa-fire"></i><span>Thói quen</span></a>
</li>

<li class="menu-item {{ Request::is('journals*') ? 'active' : '' }}">
    <a href="{{ route('journals.index') }}"><i class="fa-solid fa-book-open"></i><span>Nhật ký</span></a>
</li>

<li class="menu-item {{ Request::is('countdown*') ? 'active' : '' }}">
    <a href="{{ route('countdown.index') }}"><i class="fa-solid fa-stopwatch"></i><span>Đếm ngược</span></a>
</li>
