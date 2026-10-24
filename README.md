# Aurora — Website quảng bá sản phẩm

Đề 3 · Triển khai và Quản trị Hệ thống Phần mềm · Ngô Thị Như Quỳnh · DTC245200620 · CNTT K23D.

WordPress và MySQL qua Nginx; quản trị sản phẩm bằng WordPress Admin và database bằng phpMyAdmin. Source được xây mới, theme và plugin tách riêng.

## Khởi động trên Docker Desktop

Bật Docker Desktop, chọn Linux containers; dùng PowerShell ở thư mục repo.

```powershell
powershell -ExecutionPolicy Bypass -File scripts/start.ps1
```

Script sinh secret lần đầu, giữ secret và dữ liệu khi chạy lại. Cần Compose v2, khoảng 8 GB RAM và 15 GB đĩa trống.

| Thành phần | URL |
|---|---|
| Website | http://localhost:8080 |
| WordPress Admin | http://localhost:8080/wp-admin/ |
| phpMyAdmin | http://localhost:8081 |

Admin WordPress: `aurora_admin`; mật khẩu `.local/secrets/wp_password`. MySQL: `aurora`; mật khẩu `.local/secrets/db_password`. Không đưa secret lên Git.

## Cấu trúc

`app/`: theme và plugin. `infra/`: Nginx, MySQL, PHP và khởi tạo WordPress. `observability/`: metrics và log. `scripts/`: vận hành. `docs/`: hướng dẫn, minh chứng và báo cáo. `.local/`: secret, backup và dữ liệu phát sinh ngoài Git.

## Dừng

```powershell
powershell -ExecutionPolicy Bypass -File scripts/stop.ps1
```

Dữ liệu dùng named volume riêng của project `aurora-de3`. Không dùng prune hoặc down --volumes khi chỉ muốn dừng.
