<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life Tracker - Đăng nhập</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
</head>

<body class="auth-page w-100 p-4 d-flex align-items-center justify-content-center">
    <div class="card border-0 shadow-lg rounded-4 p-4 auth-card auth-card-login">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3 auth-brand-icon">
                <i class="fa-solid fa-bolt fs-2"></i>
            </div>

            <h4 class="fw-bold text-dark mb-1">Life Tracker</h4>
            <p class="text-muted small mb-0">Đăng nhập</p>
        </div>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            @error('email')
                <div class="alert alert-danger py-2 small border-0 shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                </div>
            @enderror

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small mb-1">Email</label>
                <input type="email" name="email" class="form-control shadow-none py-2" value="{{ old('email') }}"
                    required autofocus placeholder="Nhập email">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small mb-1">Mật khẩu</label>
                <input type="password" name="password" class="form-control shadow-none py-2" required placeholder="Nhập mật khẩu">
            </div>

            <div class="mb-4 d-flex align-items-center justify-content-between auth-login-meta">
                <div class="form-check m-0">
                    <input type="checkbox" name="remember" class="form-check-input shadow-none" id="rememberMe">
                    <label class="form-check-label text-muted small auth-remember-label" for="rememberMe">Ghi nhớ</label>
                </div>

                <a href="{{ route('signup') }}" class="text-decoration-none text-primary small">Tạo tài khoản</a>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm">
                Đăng nhập <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i>
            </button>
        </form>
    </div>
</body>

</html>
