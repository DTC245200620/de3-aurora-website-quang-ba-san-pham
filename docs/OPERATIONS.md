# Vận hành Aurora

## Tài khoản local

| Dịch vụ | Username | File mật khẩu |
|---|---|---|
| WordPress Admin | aurora_admin | .local/secrets/wp_password |
| phpMyAdmin / MySQL ứng dụng | aurora | .local/secrets/db_password |
| Grafana | admin | .local/secrets/grafana_password |

Các file không nằm trên Git. MySQL root chỉ dùng khởi tạo; exporter có tài khoản riêng. Không chụp ảnh hoặc chia sẻ nội dung secret.

## Kiểm tra

```powershell
. ./scripts/compose.ps1
Invoke-Compose ps -a
Invoke-Compose logs --tail 60 nginx wordpress mysql
./scripts/verify.ps1
```

Ba service `wordpress-init`, `log-init`, `positions-init` hoàn tất rồi `Exited (0)` là bình thường. Những service còn lại phải chạy. Verifier tạo request 404 và đăng nhập thất bại phục vụ kiểm tra log.

## Backup

```powershell
./scripts/backup.ps1
```

Script tạm dừng website và phpMyAdmin để chốt database cùng uploads, tạo thư mục `.local/backups/THOI_GIAN-ID` gồm `database.sql`, `uploads.tar.gz` và `SHA256SUMS`, rồi bật lại ứng dụng. Nếu thao tác lỗi, khối finally vẫn yêu cầu khởi động lại stack.

## Restore

```powershell
./scripts/restore.ps1 -BackupId TEN_THU_MUC_BACKUP
```

Nhập `RESTORE` khi được hỏi. Script xác minh checksum, thay database và uploads của riêng Aurora. Các thay đổi sau thời điểm backup sẽ được thay thế; tạo backup mới trước khi restore nếu cần giữ chúng.

Backup giữ nội dung và ảnh; theme/plugin/config được phục hồi bằng Git. MySQL user và secret phải được giữ đúng với database volume, không tự thay secret khi đang tái sử dụng volume.

## Hardening

Nginx chạy UID 101 ở cổng cao, filesystem chỉ đọc, bỏ capabilities. Prometheus/exporter chạy UID 65534, Grafana UID 472, Loki UID 10001 và Promtail UID 65534. WordPress official Apache dùng master root để khởi tạo và worker www-data; MySQL entrypoint hạ quyền cho tiến trình mysqld. Không ép user làm hỏng entrypoint của hai image này.

Database nằm trên mạng internal; MySQL không publish 3306. Các cổng công cụ chỉ bind 127.0.0.1. Secret chỉ được mount vào service cần dùng. WordPress tắt trình sửa/cài code trong Admin; thao tác cập nhật code thực hiện qua Git và container image.

cAdvisor là ngoại lệ privileged, đọc rootfs, cgroup và Docker runtime của host để lấy CPU/RAM và nhãn Compose. Không publish cổng cAdvisor hoặc exporter. Công cụ backup/restore chạy theo yêu cầu, giới hạn ở volume Aurora và thư mục backup; restore cần CHOWN/DAC_OVERRIDE/FOWNER để phục hồi quyền uploads.

## Log và dung lượng

Log Docker xoay tối đa ba file, mỗi file 5 MB. Sidecar logrotate kiểm tra mỗi phút, xoay file ứng dụng/Nginx bằng copy-truncate ở ngưỡng 5 MB và giữ ba bản. Loki giữ bảy ngày; Prometheus giữ bảy ngày, tối đa 512 MB. Volume log và positions của Promtail tồn tại qua restart.

## Lỗi thường gặp

- Secret chưa có: chạy prepare; không nhập mật khẩu trực tiếp vào compose.
- MySQL unhealthy: kiểm tra log, mật khẩu hiện có và dung lượng đĩa. Không xóa volume để che lỗi.
- Nginx 502: kiểm tra WordPress và MySQL; Nginx dùng Docker DNS để cập nhật địa chỉ backend.
- Dashboard trống: đợi ít nhất hai lượt scrape 15 giây, kiểm tra Targets và nhãn project.
- Log chưa đến: tạo traffic bằng verify, kiểm tra Promtail/Loki và quyền đọc file.
- Đổi URL: sửa SITE_URL trong .env, chạy lại start; cổng host được cấu hình trong compose.yaml.
