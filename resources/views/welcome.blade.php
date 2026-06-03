
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Life Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="text-center">
        <div class="display-1 text-primary mb-3"><i class="fa-solid fa-bolt"></i></div>
        <h1 class="fw-bold text-dark mb-3">Life Tracker</h1>
        <p class="text-muted mb-4">Hệ thống quản lý thói quen và công việc cá nhân</p>

        @auth
            @if((auth()->user()->role ?? null) === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">Vào Bảng điều khiển</a>
            @else
                <a href="{{ route('overview.index') }}" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">Vào Bảng điều khiển</a>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm me-2">Đăng nhập</a>
        @endauth
    </div>
</body>
</html>
