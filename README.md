# LifeTracker

LifeTracker là ứng dụng web Laravel phục vụ đồ án sinh viên, giúp người dùng theo dõi công việc, thói quen, nhật ký cá nhân và các mốc đếm ngược. Dự án giữ scope gọn, dễ chạy local và dễ giải thích khi bảo vệ.

## Chức năng hiện có

### Người dùng

- Đăng ký, đăng nhập, đăng xuất bằng email và mật khẩu.
- Dashboard cá nhân hiển thị tổng quan công việc hôm nay, thói quen, nhật ký trong tháng, countdown sắp tới và biểu đồ năng suất.
- Quản lý task cơ bản:
  - Thêm, sửa, xóa task.
  - Cập nhật trạng thái hoàn thành.
  - Chọn độ ưu tiên, deadline và tag.
  - Tìm kiếm, lọc và phân trang.
- Quản lý habit cơ bản:
  - Thêm, sửa, xóa habit.
  - Tick hoàn thành theo ngày.
  - Theo dõi streak và tổng số lần hoàn thành.
  - Xem lịch sử hoàn thành theo tháng.
- Quản lý journal:
  - Thêm, sửa, xóa nhật ký.
  - Tìm kiếm và lọc theo ngày.
- Quản lý countdown:
  - Thêm, sửa, xóa sự kiện đếm ngược.
  - Chọn ngày sự kiện và màu hiển thị.

### Admin

- Dashboard admin với thống kê user và mức sử dụng tính năng.
- Biểu đồ tăng trưởng user và hoạt động gần đây.
- Export báo cáo CSV.
- Quản lý user:
  - Xem danh sách, tìm kiếm, lọc và phân trang.
  - Thêm admin.
  - Đổi role/status.
  - Xóa user, có bảo vệ admin cuối cùng.

## Công nghệ

- PHP 8.2+
- Laravel 12
- Blade
- Bootstrap 5
- CSS thuần
- JavaScript thuần
- Mặc định dùng SQLite; có thể đổi sang MySQL qua `.env`
- PHPUnit

## Cấu trúc chính

```txt
app/
  Http/Controllers/
  Http/Middleware/
  Models/

database/
  migrations/
  seeders/
  factories/

resources/views/
  auth/
  layouts/
  client/
  admin/
  vendor/pagination/

public/
  css/
    layouts/
    pages/
  js/
    layouts/
    pages/

routes/
  web.php
```

## Chạy local

Yêu cầu máy đã có PHP 8.2+, Composer và extension SQLite cho PHP.

1. Cài dependency PHP:

```bash
composer install
```

2. Tạo file môi trường:

```bash
cp .env.example .env
```

Mặc định `.env.example` dùng SQLite:

```env
DB_CONNECTION=sqlite
```

3. Tạo file database SQLite trước khi migrate.

Trên macOS/Linux:

```bash
mkdir -p database
touch database/database.sqlite
```

Trên Windows PowerShell:

```powershell
New-Item -ItemType File -Force database/database.sqlite
```

4. Tạo app key:

```bash
php artisan key:generate
```

5. Tạo bảng và dữ liệu demo:

```bash
php artisan migrate:fresh --seed
```

6. Chạy server local:

```bash
php artisan serve
```

Mở trình duyệt tại:

```bash
http://127.0.0.1:8000
```

Nếu muốn dùng MySQL thay SQLite, sửa các biến `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` trong `.env`, tạo database tương ứng, rồi chạy lại:

```bash
php artisan migrate:fresh --seed
```

## Tài khoản demo

Seeder tạo sẵn tài khoản admin:

```txt
Email: admin@lifetracker.com
Password: 123456
```

Seeder cũng tạo một vài user thường và dữ liệu mẫu cho task, tag, habit, journal và countdown.

## Kiểm tra

```bash
php artisan test
npm run build
```

`npm run build` hiện dùng để kiểm tra cú pháp các file JavaScript trong `public/js`.
