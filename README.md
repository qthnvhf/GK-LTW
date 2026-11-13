# BÀI KIỂM TRA GIỮA HỌC PHẦN – CÔNG NGHỆ VÀ LẬP TRÌNH WEB

**Đề tài:** Website quản lý người dùng bằng **PHP thuần + MySQL**. Không sử dụng framework, phù hợp với kiến thức lập trình web năm 2.

## 1. Hướng dẫn chạy trên XAMPP

1. Giải nén `QLNhanSu_PHP_Nam2.zip`.
2. Chép **cả thư mục** `QLNhanSu_PHP` vào `C:\xampp\htdocs\` để có đường dẫn `C:\xampp\htdocs\QLNhanSu_PHP\`.
3. Mở XAMPP Control Panel → bật **Apache** và **MySQL**.
4. Vào **http://localhost/phpmyadmin** → tab **Import (Nhập)** → chọn file `QLNhanSu_PHP/database/QLNhanSu.sql` → nhấn **Import / Go**. File SQL tự tạo database `QLNhanSu`, bảng `Users` và chèn 4 người dùng mẫu.
5. Mở **frontend:** http://localhost/QLNhanSu_PHP/
6. Mở **admin/backend:** http://localhost/QLNhanSu_PHP/admin/login.php

**Đăng nhập trang admin demo:** `admin` / `Admin@123`. Tài khoản quản trị này được đặt riêng trong file cấu hình để không phải thêm bảng ngoài đề bài. Bốn người dùng mẫu có mật khẩu `User@12345` (đã được mã hóa trong cơ sở dữ liệu). Không cần đăng nhập bằng những tài khoản này vì đề không yêu cầu đăng nhập người dùng.

Nếu MySQL của bạn có mật khẩu, sửa các biến `DB_USER`, `DB_PASS` tại `includes/config.php`. Nếu bạn đổi tên thư mục trong `htdocs`, hãy đổi URL localhost cho phù hợp.

## 2. Đối chiếu 4 yêu cầu của đề

| Yêu cầu | Chức năng đã làm | File |
| --- | --- | --- |
| **Câu 1 (4 điểm)** – Thêm dữ liệu Users | Form thêm email, mật khẩu, ảnh, dùng câu lệnh `INSERT`; có sẵn 4 dòng mẫu sau khi import SQL | `admin/create.php`, `database/QLNhanSu.sql` |
| **Câu 2 (4 điểm)** – Hiển thị ở frontend | Lấy dữ liệu thật bằng `SELECT`, hiển thị ID, email, ảnh, trạng thái mật khẩu; tìm kiếm theo email | `index.php` |
| **Câu 3 (1 điểm)** – Responsive | CSS Grid/Flex, giao diện tự co giãn cho desktop, tablet, điện thoại | `assets/css/style.css` |
| **Câu 4 (1 điểm)** – Sửa, xóa ở backend | Sửa bằng `UPDATE`; xóa bằng `DELETE` và hộp thoại xác nhận | `admin/index.php`, `admin/edit.php`, `admin/delete.php` |

**Tính năng thêm:** đăng nhập khu vực admin bằng Session, tải ảnh đại diện, xem trước ảnh, thông báo thành công/thất bại, tìm kiếm và kiểm tra dữ liệu nhập. Mật khẩu lưu bằng `password_hash()` và truy vấn sử dụng prepared statements để chống SQL injection.

## 3. Cách demo từng câu

**Câu 1 – Thêm:** Đăng nhập admin → **Thêm người dùng** → nhập email, mật khẩu và ảnh → lưu. Vào phpMyAdmin → `QLNhanSu` → `Users` → **Browse**, sẽ thấy dòng vừa thêm.

**Câu 2 – Hiển thị:** Mở trang chủ. Danh sách hiển thị trực tiếp từ MySQL; thêm hay sửa trong admin rồi tải lại frontend để thấy dữ liệu thay đổi. Thử tìm kiếm theo email.

**Câu 3 – Responsive:** Mở trang chủ trong Chrome → F12 → **Toggle device toolbar** (Ctrl + Shift + M) → chọn kích thước điện thoại. Bố cục sẽ chuyển sang 2 cột/thanh dọc tùy kích thước.

**Câu 4 – Sửa/xóa:** Tại `admin/index.php`, bấm **Sửa** một người dùng và lưu; kiểm tra lại frontend/phpMyAdmin. Bấm **Xóa** và xác nhận; kiểm tra bản ghi đã biến mất.

**Quan trọng:** Đề yêu cầu ảnh chụp kết quả hoặc video demo có thay đổi cơ sở dữ liệu. Xem file `HUONG_DAN_CHUP_ANH.md` để tự ghi lại kết quả **thật sau khi chạy XAMPP**.

## 4. Cấu trúc mã nguồn

```text
QLNhanSu_PHP/
├── index.php                   # Frontend: SELECT + tìm kiếm
├── admin/
│   ├── login.php               # Đăng nhập trang admin
│   ├── logout.php              # Đăng xuất
│   ├── index.php               # Danh sách Users trong backend
│   ├── create.php              # INSERT
│   ├── edit.php                # UPDATE
│   └── delete.php              # DELETE
├── includes/
│   ├── config.php              # Cấu hình MySQL, tài khoản admin
│   ├── bootstrap.php           # Khởi tạo Session
│   ├── database.php            # Kết nối MySQL
│   └── functions.php           # Các hàm dùng chung
├── database/QLNhanSu.sql       # Script SQL + dữ liệu mẫu
├── assets/
│   ├── css/style.css           # Responsive CSS
│   ├── js/main.js              # Xem trước ảnh / xác nhận xóa
│   └── img/                    # Ảnh minh họa SVG
├── uploads/                    # Thư mục chứa ảnh người dùng tải lên
└── HUONG_DAN_CHUP_ANH.md       # Hướng dẫn minh chứng bài thi
```

## 5. Cơ sở dữ liệu

Tên **database `QLNhanSu`**, tên **bảng `Users`** (giữ đúng chữ hoa/thường trong đề), với 4 cột:

- `admin_id`: `INT(10) UNSIGNED`, khóa chính, `AUTO_INCREMENT`.
- `admin_email`: `VARCHAR(255)`, không trùng email.
- `admin_password`: `VARCHAR(255)`, lưu mật khẩu đã băm.
- `images`: `VARCHAR(255)`, lưu đường dẫn ảnh.

## 6. Lưu ý kỹ thuật

- Cần **PHP 8.0+**, **MySQL** và các extension PHP **mysqli**, **fileinfo** (XAMPP thông thường đã có).
- Ảnh tải lên hỗ trợ JPG/PNG/WEBP/GIF tối đa 2 MB. Apache cần quyền ghi trong thư mục `uploads`.
- Vì lý do bảo mật, frontend **không in mật khẩu**. Bảng vẫn có đầy đủ cột `admin_password` theo đề.
- Tài khoản quản trị mặc định **chỉ dành cho bài demo trên localhost**. Đừng triển khai công khai khi chưa đổi mật khẩu và thiết kế xác thực/ phân quyền phù hợp.
- Nếu import SQL lại, 4 dòng mẫu được chèn bằng `INSERT IGNORE` để tránh trùng ID/email. Các dữ liệu mới bạn tự thêm vẫn giữ nguyên.
