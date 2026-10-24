# Kịch bản demo và minh chứng

1. Mở repo public, xem cấu trúc source và các commit theo giai đoạn.
2. Chạy start.ps1, xem ps -a; giải thích service init kết thúc mã 0.
3. Truy cập trang chủ, danh sách, chi tiết, giới thiệu, liên hệ và chế độ mobile.
4. Đăng nhập WordPress; thêm một sản phẩm, đặt SKU/giá/ảnh đại diện, mở trang public; sửa giá và kiểm tra lại; chuyển sản phẩm thử vào thùng rác.
5. Đăng nhập phpMyAdmin bằng user aurora, mở database aurora, xem dữ liệu trong wp_posts/wp_postmeta và ảnh trong wp_posts dạng attachment.
6. Mở Prometheus Targets: bốn job UP. Mở Grafana → Aurora → Tổng quan hệ thống: xem CPU/RAM theo service, request Nginx, kết nối/truy vấn MySQL.
7. Chạy verify.ps1 tạo traffic và lỗi mẫu. Mở Grafana → Aurora → Log tập trung hoặc Explore → Loki.
8. Chạy ba LogQL bên dưới, đối chiếu kết quả có thời gian và service.
9. Giải thích mạng internal, cổng loopback, non-root, secret, security headers và ngoại lệ cAdvisor.
10. Trình bày backup/restore và nhật ký kiểm tra Desktop.

## LogQL

```logql
{project="aurora-de3",service="nginx",stream="access"} | json

{project="aurora-de3",service="nginx",stream="access"} | json | status >= 400

{project="aurora-de3",service="wordpress",stream="events"} | json | event="login_failed"
```

Log ứng dụng ghi sự kiện sản phẩm, khởi tạo catalog và đăng nhập thất bại; không ghi username/password đăng nhập. Log Nginx dùng URI không có query string.

## Ảnh

Ảnh trong screenshots được chụp từ trình duyệt trong đợt xây dựng mới: trạng thái repo vừa tạo, source commit đầu, website, Admin, database, Prometheus/Grafana, cấu hình monitoring, logging và lịch sử commit. Báo cáo dùng ảnh theo từng giai đoạn, kèm chú thích giải thích nội dung.
