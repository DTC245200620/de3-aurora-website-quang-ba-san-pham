# Chạy Aurora với Docker Engine trong Ubuntu WSL2

Hướng dẫn dùng Ubuntu 24.04 LTS trên WSL2 và Docker Engine cài trực tiếp trong Ubuntu.

## Chuẩn bị Ubuntu

Trong PowerShell quản trị:

```powershell
wsl --install -d Ubuntu-24.04
wsl --set-version Ubuntu-24.04 2
wsl --list --verbose
```

Mở Ubuntu, hoàn tất tạo tài khoản Linux. Tắt Docker Desktop trước khi chạy stack trong Ubuntu để tránh tranh chấp cổng. Trong Docker Desktop Settings → Resources → WSL Integration, không bật Integration cho distro Ubuntu dùng Engine riêng này.

Kiểm tra systemd:

```sh
ps -p 1 -o comm=
```

Nếu chưa là systemd, thêm vào `/etc/wsl.conf`, giữ nguyên các cấu hình khác:

```ini
[boot]
systemd=true
```

Thoát Ubuntu, chạy `wsl --terminate Ubuntu-24.04` trong PowerShell rồi mở lại.

## Cài Docker từ kho chính thức

Trong Ubuntu:

```sh
sudo apt-get update
sudo apt-get install -y ca-certificates curl git
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

sudo tee /etc/apt/sources.list.d/docker.sources >/dev/null <<EOF
Types: deb
URIs: https://download.docker.com/linux/ubuntu
Suites: $(. /etc/os-release && echo "$VERSION_CODENAME")
Components: stable
Architectures: $(dpkg --print-architecture)
Signed-By: /etc/apt/keyrings/docker.asc
EOF

sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
sudo systemctl enable --now docker
sudo usermod -aG docker "$USER"
```

Thoát phiên Ubuntu rồi mở lại để nhận nhóm docker. Nhóm docker có quyền quản lý daemon.

## Xác định đúng Docker Engine

```sh
unset DOCKER_HOST DOCKER_CONTEXT
docker context use default
docker context inspect default
docker info --format '{{.OperatingSystem}} | {{.Name}} | {{.DockerRootDir}}'
systemctl is-active docker
docker compose version
```

Endpoint dùng socket Linux `unix:///var/run/docker.sock`; daemon là Engine trong Ubuntu, không phải Docker Desktop. Các volume của hai daemon độc lập.

## Clone và chạy

Clone vào filesystem Linux trong `~/projects`, tránh `/mnt/c` để giữ quyền file và hiệu năng.

```sh
mkdir -p ~/projects
cd ~/projects
git clone https://github.com/DTC245200620/de3-aurora-website-quang-ba-san-pham.git
cd de3-aurora-website-quang-ba-san-pham
sh scripts/start.sh
sh scripts/verify.sh
```

Truy cập từ Windows: website `http://localhost:8080`, phpMyAdmin `http://localhost:8081`, Grafana `http://localhost:3002`, Prometheus `http://localhost:9092`. Lần đầu tạo bộ secret riêng của Ubuntu.

## Vận hành

```sh
sh scripts/backup.sh
sh scripts/restore.sh TEN_THU_MUC_BACKUP
sh scripts/stop.sh
```

Khôi phục yêu cầu nhập `RESTORE`. Dữ liệu nằm trong named volume của Engine Ubuntu; backup ở `.local/backups`. Khi chuyển môi trường, dùng backup database + uploads; không trỏ hai daemon vào cùng thư mục dữ liệu MySQL.

## Xử lý lỗi

- Không truy cập được daemon: kiểm tra `systemctl status docker`, nhóm docker và context.
- Permission denied khi đọc secret: chạy lại `sh scripts/prepare.sh`; không chạy repo từ NTFS.
- Cổng đã dùng: dừng stack tương ứng trên Desktop; kiểm tra `ss -ltn`.
- cAdvisor thiếu dữ liệu: kiểm tra Docker root directory, cgroup và log service; các mount trong cấu hình trỏ tới host chạy Engine.
- Windows không mở được localhost: kiểm tra `curl http://localhost:8080` bên trong Ubuntu, localhost forwarding của WSL và firewall Windows.

Tài liệu nền: [Docker Engine trên Ubuntu](https://docs.docker.com/engine/install/ubuntu/), [systemd trong WSL](https://learn.microsoft.com/en-us/windows/wsl/systemd).
