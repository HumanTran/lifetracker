<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life Tracker - Đăng ký</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
</head>

<body class="auth-page w-100 p-4 d-flex align-items-center justify-content-center">
    <div class="card border-0 shadow-lg rounded-4 p-4 auth-card auth-card-signup">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3 auth-brand-icon">
                <i class="fa-solid fa-user-plus fs-2"></i>
            </div>

            <h4 class="fw-bold text-dark mb-0">Tạo tài khoản</h4>
        </div>

        <form method="POST" action="{{ route('signup.post') }}">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger py-2 small border-0 shadow-sm" role="alert">
                    <div class="fw-semibold mb-1">Có lỗi xảy ra:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small mb-1">Email</label>
                <input type="email" name="email" class="form-control shadow-none py-2" value="{{ old('email') }}"
                    required autofocus autocomplete="email" placeholder="Nhập email">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small mb-1">Mật khẩu</label>
                <input type="password" name="password" class="form-control shadow-none py-2" required autocomplete="new-password"
                    placeholder="Tối thiểu 6 ký tự">
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-secondary small mb-1">Nhập lại mật khẩu</label>
                <input type="password" name="password_confirmation" class="form-control shadow-none py-2" required autocomplete="new-password"
                    placeholder="Nhập lại mật khẩu">
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm">
                Đăng ký <i class="fa-solid fa-check ms-1"></i>
            </button>
        </form>

        <div class="text-center mt-3 auth-switch-link">
            <span class="text-muted small">Đã có tài khoản?</span>
            <a href="{{ route('login') }}" class="text-decoration-none text-primary small">Đăng nhập</a>
        </div>
    </div>
</body>

</html>
