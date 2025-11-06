# Hướng dẫn chạy Laravel với Docker

## Yêu cầu
- Docker Desktop đã được cài đặt
- Docker Compose đã được cài đặt (thường đi kèm với Docker Desktop)

## Các bước chạy:

### 1. Build và khởi động containers
```bash
docker-compose up -d --build
```

### 2. Cài đặt dependencies
```bash
docker-compose exec php composer install
```

### 3. Tạo file .env (nếu chưa có)
```bash
cp .env.example .env
```

### 4. Cấu hình .env
Đảm bảo các thông tin database trong .env:
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=lms1
DB_USERNAME=root
DB_PASSWORD=root
```

### 5. Tạo key cho ứng dụng
```bash
docker-compose exec php php artisan key:generate
```

### 6. Chạy migrations
```bash
docker-compose exec php php artisan migrate
```

### 7. Tạo storage link
```bash
docker-compose exec php php artisan storage:link
```

## Truy cập ứng dụng:
- Website: http://localhost:8000
- PHPMyAdmin: http://localhost:8080
- MySQL: localhost:3307

## Các lệnh thường dùng:

### Xem logs
```bash
docker-compose logs -f php
docker-compose logs -f nginx
docker-compose logs -f mysql
```

### Chạy artisan commands
```bash
docker-compose exec php php artisan [command]
```

### Chạy composer
```bash
docker-compose exec php composer [command]
```

### Dừng containers
```bash
docker-compose down
```

### Dừng và xóa volumes (xóa database)
```bash
docker-compose down -v
```

### Rebuild containers
```bash
docker-compose up -d --build --force-recreate
```




