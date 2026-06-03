<li class="menu-item">
    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0 w-100">
        @csrf

        <button type="submit" class="sidebar-menu-btn sidebar-logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Đăng xuất</span>
        </button>
    </form>
</li>
