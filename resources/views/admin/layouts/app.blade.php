@extends('layouts.shell')

@section('title', 'Life Tracker - Quản trị')

@section('sidebar_primary')
    <li class="menu-item {{ Request::is('admin') ? 'active' : '' }}">
        <a href="{{ route('admin.dashboard') }}">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Tổng quan</span>
        </a>
    </li>

    <li class="menu-item {{ Request::is('admin/users*') ? 'active' : '' }}">
        <a href="{{ route('admin.users.index') }}">
            <i class="fa-solid fa-users"></i>
            <span>Người dùng</span>
        </a>
    </li>
@endsection

@section('sidebar_secondary')
    <li class="menu-item">
        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0 w-100">
            @csrf

            <button type="submit" class="sidebar-menu-btn sidebar-logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Đăng xuất</span>
            </button>
        </form>
    </li>
@endsection

@section('topbar_left')
    <div class="page-info">
        <h2 class="mb-0 text-dark admin-page-title">
            @yield('page_title', 'Dashboard')
        </h2>
    </div>
@endsection

@section('topbar_actions')
@endsection
