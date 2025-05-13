# <p align = 'center'>**ProMeet - Nền Tảng Đặt Phòng Họp & Quản Trị Doanh Nghiệp**</p>

## Mục lục
- [Giới thiệu](#giới-thiệu)
- [Tính năng chính](#tính-năng-chính)
- [Tài liệu chi tiết](#tài-liệu-chi-tiết)
- [Cài đặt](#cài-đặt)
- [Cấu trúc thư mục](#cấu-trúc-thư-mục)
- [Sử dụng](#sử-dụng)
- [Bảo mật](#bảo-mật)
- [Liên hệ](#liên-hệ)

## Giới thiệu
ProMeet là hệ thống quản lý đặt phòng họp được thiết kế để giúp các tổ chức sắp xếp và điều phối việc sử dụng phòng họp một cách hiệu quả. Với giao diện trực quan và thân thiện, hệ thống cho phép người dùng dễ dàng xem tình trạng phòng trống, đặt lịch họp và theo dõi lịch sử sử dụng phòng theo thời gian thực.

Được xây dựng trên nền tảng công nghệ web hiện đại, ProMeet cung cấp:
- Hệ thống xác thực người dùng bảo mật và phân quyền chi tiết
- Chức năng đặt phòng linh hoạt theo từng khung giờ
- Quản lý thông tin cá nhân và lịch sử đặt phòng
- Trang quản trị dành cho admin với các tính năng:
  - Quản lý danh sách phòng họp và tình trạng sử dụng
  - Theo dõi và duyệt yêu cầu đặt phòng
  - Quản lý tài khoản người dùng
  - Xem báo cáo thống kê việc sử dụng phòng họp

ProMeet giúp đơn giản hóa quy trình đặt và quản lý phòng họp, giảm thiểu việc chồng chéo lịch họp và tận dụng hiệu quả các phòng họp trong tổ chức của bạn.

## Tính năng chính
- Xác thực và phân quyền người dùng
- Quản lý và đặt phòng họp theo khung giờ
- Theo dõi lịch sử đặt phòng
- Quản lý thông tin cá nhân
- Trang quản trị cho admin:
  - Quản lý thông tin phòng họp
  - Quản lý thông tin đặt phòng
  - Cập nhật trạng thái đặt phòng
  - Quản lý người dùng
  - Thống kê và báo cáo sử dụng

## Tài liệu chi tiết
Để xem thông tin chi tiết về thiết kế và kiến trúc hệ thống, vui lòng tham khảo [Tài liệu thiết kế chi tiết](docs/report.pdf), bao gồm:
- ERD & Cấu trúc CSDL
- Use Case Diagram & Đặc tả use case
- Activity Diagram
- Kiến trúc hệ thống
- Design Pattern được sử dụng
- Giao diện và Demo ứng dụng
- Và các thông tin kỹ thuật khác

## Cài đặt

### Bước 1: Cài đặt XAMPP
XAMPP là phần mềm tích hợp Apache, MySQL, PHP và Perl giúp tạo máy chủ web cục bộ. Truy cập trang chủ XAMPP tại https://www.apachefriends.org/index.html, chọn phiên bản phù hợp với hệ điều hành và tiến hành cài đặt như phần mềm thông thường.

### Bước 2: Clone mã nguồn ProMeet
Sau khi cài đặt XAMPP, bạn cần tải mã nguồn ứng dụng ProMeet từ GitHub. Mở Terminal (hoặc Git Bash trên Windows), di chuyển đến thư mục htdocs trong thư mục cài đặt XAMPP, sau đó chạy lệnh:

```bash
cd C:/xampp/htdocs
git clone https://github.com/tacsquang/ProMeet.git
```

Sau khi clone thành công, mã nguồn sẽ nằm trong thư mục htdocs/ProMeet.

### Bước 3: Tạo cơ sở dữ liệu MySQL
1. Mở trình duyệt và truy cập http://localhost/phpmyadmin
2. Nhấp vào tab Import
3. Chọn file SQL có sẵn trong thư mục mã nguồn (chứa các lệnh DROP DATABASE, CREATE DATABASE, CREATE TABLE)
4. Nhấn Go để thực hiện import

### Bước 4: Khởi động Apache và MySQL
Mở XAMPP Control Panel, nhấn nút Start cho hai dịch vụ Apache và MySQL.

### Bước 5: Cấu hình kết nối cơ sở dữ liệu
Mở file cấu hình trong thư mục ứng dụng tại đường dẫn: `config/database.php` và kiểm tra hoặc điều chỉnh thông tin như sau:

```php
return [
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'prooo_meet',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];
```

**Lưu ý**: Trong XAMPP mặc định, tài khoản MySQL là root và không có mật khẩu. Nếu bạn đã đặt mật khẩu MySQL, hãy cập nhật lại phần password tương ứng.

## Cấu trúc thư mục
```
ProMeet/
├── app/                    # Thư mục chứa mã nguồn chính
│   ├── Controllers/       # Xử lý logic điều khiển
│   ├── Models/           # Xử lý tương tác với CSDL
│   ├── views/            # Giao diện người dùng
│   ├── Core/            # Các thành phần cốt lõi của ứng dụng
│   │   ├── App.php        # Khởi tạo và quản lý ứng dụng
│   │   ├── Router.php     # Xử lý định tuyến
│   │   ├── Database.php   # Kết nối và thao tác CSDL
│   │   ├── View.php       # Xử lý hiển thị giao diện
│   │   ├── Container.php  # Quản lý dependency injection
│   │   ├── Utils.php      # Các tiện ích
│   │   └── LogService.php # Xử lý ghi log
│   └── bootstrap.php    # File khởi động ứng dụng
├── config/               # Cấu hình ứng dụng
├── public/              # Thư mục public (CSS, JS, images)
├── docs/                # Tài liệu
└── logs/                # File logs
```

## Sử dụng
1. Truy cập ứng dụng qua web browser: `http://localhost/ProMeet/public`
2. Đăng nhập với tài khoản mặc định:
   - Username: admin
   - Password: admin123

## Bảo mật
- Sử dụng prepared statements cho truy vấn database
- Mã hóa mật khẩu với bcrypt
- Xác thực và phân quyền người dùng
- Bảo vệ chống CSRF

## Liên hệ
Tran Quang Tac - tac.tranquang@hcmut.edu.vn
Project Link: [https://github.com/tacsquang/ProMeet](https://github.com/tacsquang/ProMeet)
