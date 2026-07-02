# 🍦 Scoop Shop — Ice Cream E-Commerce

เว็บไซต์ร้านขายไอศกรีมออนไลน์แบบ **full-stack** พัฒนาด้วย PHP รองรับทั้ง **ฝั่งลูกค้า** และ **ฝั่งผู้ขาย/แอดมิน** ครบวงจรตั้งแต่เลือกสินค้า สั่งซื้อ จ่ายเงิน ไปจนถึงการจัดการร้านและติดตามสถานะการจัดส่ง

---

## 📖 ภาพรวม (Overview)

ระบบแบ่งเป็น 2 ส่วนที่ทำงานเชื่อมกัน:

- **ฝั่งลูกค้า (User)** — เลือกซื้อสินค้า, ค้นหาแบบเรียลไทม์, ใส่ตะกร้า/wishlist, สั่งซื้อ, เลือกวิธีจ่ายเงิน และติดตามสถานะออเดอร์
- **ฝั่งแอดมิน/ผู้ขาย (Admin/Seller)** — dashboard สรุปยอด, จัดการสินค้า (เพิ่ม/แก้/ลบ), จัดการออเดอร์และอัปเดตสถานะจัดส่ง, ดูข้อมูลผู้ใช้และข้อความติดต่อ

> ตัวอย่างการเชื่อมกัน: เมื่อแอดมินกด **"order deliverd"** สถานะฝั่งลูกค้าจะเปลี่ยนเป็น **Delivered** ทันที

---

## ✨ ฟีเจอร์เด่น (Features)

### ฝั่งลูกค้า
- 🔍 **Live Search** — พิมพ์แล้วค้นหาสินค้าทันที ไม่ต้องกด Enter (AJAX)
- 🛒 **ตะกร้าสินค้า & Wishlist**
- 💳 **ระบบชำระเงินสมจริง** — เลือกได้หลายวิธี (Cash on Delivery, บัตรเครดิต, Net Banking, UPI/PayPal, Paytm)
  - แสดง **บัตรเครดิตตัวอย่างแบบสด** ขณะกรอกเลขบัตร/ชื่อ/วันหมดอายุ
  - สร้าง **QR code** สำหรับวิธีที่ต้องสแกนจ่าย (อ้างอิงยอดจริง)
- 📦 **รวมออเดอร์** — สินค้าที่จ่ายพร้อมกันถูกจัดเป็น "ออเดอร์เดียว" (order group)
- 🚚 **ติดตามสถานะ** — In Progress / Delivered / Canceled พร้อมตัวกรอง
- 📄 **แบ่งหน้า (Pagination)** ทุกหน้าที่แสดงรายการ
- 🔐 ต้องล็อกอินก่อนสั่งซื้อ + ป้องกันสั่งสินค้าที่ **หมดสต็อก**

### ฝั่งแอดมิน/ผู้ขาย
- 📊 **Dashboard** สรุปจำนวนสินค้า/ออเดอร์/ผู้ใช้ พร้อมปุ่มกรอง
- 🍨 **จัดการสินค้า** — เพิ่ม/แก้ไข/ลบ + กรอง active/deactive
- 📋 **จัดการออเดอร์** — อัปเดตสถานะจ่ายเงิน/จัดส่ง, ลบออเดอร์ (ทั้งกลุ่ม), กรอง + แบ่งหน้า
- 👥 ดูรายชื่อผู้ใช้/ผู้ขาย และข้อความติดต่อ

---

## 🛠️ เทคโนโลยีที่ใช้ (Tech Stack)

| ส่วน | เทคโนโลยี |
|---|---|
| Backend | PHP (PDO-style data layer) |
| Frontend | HTML, CSS, Vanilla JavaScript (fetch/AJAX) |
| ฐานข้อมูล | Mock DB เก็บใน PHP Session (ออกแบบให้ต่อ MySQL จริงได้) |
| Libraries | SweetAlert, Boxicons, Font Awesome |
| QR | api.qrserver.com |

> **หมายเหตุ:** โปรเจกต์นี้ใช้ mock database ที่เก็บข้อมูลใน session เพื่อให้รันได้ทันทีโดยไม่ต้องติดตั้ง MySQL — โค้ดใช้ pattern แบบ PDO (`prepare` / `execute` / prepared statements) จึงพร้อมเปลี่ยนไปต่อ MySQL จริงได้

---

## 🚀 วิธีรัน (Getting Started)

ต้องมี **PHP 7.4+** ติดตั้งในเครื่อง

```bash
# วิธีที่ 1: ดับเบิลคลิกไฟล์
run-php-server.bat

# วิธีที่ 2: รันผ่าน terminal
php -S localhost:8000
```

จากนั้นเปิดเบราว์เซอร์ไปที่:

- **ฝั่งลูกค้า:** http://localhost:8000/home.php
- **ฝั่งแอดมิน:** http://localhost:8000/admin%20panel/login.php

> รีเซ็ตข้อมูลตัวอย่างได้โดยเติม `?reset_mock` ต่อท้าย URL

---

## 🔑 บัญชีทดสอบ (Demo Accounts)

| Role | Email | Password |
|---|---|---|
| ลูกค้า (Customer) | `customer@example.com` | `123456` |
| แอดมิน (Seller) | `testAdmin@gmail.com` | `123456` |

> สมัครลูกค้าใหม่ได้ที่ `register.php` — ระบบจะออก User ID แบบรันต่อเนื่อง (c0001, c0002, …)

---

## 📂 โครงสร้างโปรเจกต์ (Structure)

```
icecream_shop/
├── home.php, menu.php, checkout.php ...   # หน้าฝั่งลูกค้า
├── order.php, view_order.php, pay.php      # ออเดอร์ & ชำระเงิน
├── component/                              # ส่วนที่ใช้ซ้ำ
│   ├── connect.php                         # data layer + mock DB
│   ├── user_header.php, footer.php
│   ├── user_auth.php, admin_auth.php       # auth guards
│   ├── add_cart.php, search_cards.php ...
├── admin panel/                           # หน้าฝั่งแอดมิน
│   ├── dashboard.php, add_products.php
│   ├── admin_order.php, view_product.php ...
├── css/                                   # user_style.css, admin_style.css
├── js/                                    # user_script.js, admin_script.js
└── uploaded_files/                        # รูปสินค้า/โปรไฟล์
```

---

## 🔒 หมายเหตุด้านความปลอดภัย & แนวทางพัฒนาต่อ (Future Work)

โปรเจกต์นี้เน้นการเรียนรู้ full-stack สิ่งที่ควรเพิ่มหากนำไปใช้งานจริง:

- 🔐 เปลี่ยน password hashing จาก `sha1` → `password_hash()` (bcrypt)
- 🍪 ย้ายการยืนยันตัวตนฝั่งลูกค้าจาก cookie → **session/token**
- 🛡️ เพิ่ม **CSRF protection** และ validation ฝั่ง server ให้ครบ
- 🗄️ เชื่อม **MySQL จริง** แทน mock DB
- 📦 ตัด stock อัตโนมัติเมื่อมีการสั่งซื้อ
- 🏗️ แยกโครงสร้างเป็น **MVC** หรือใช้ framework (เช่น Laravel)

---

## 👩‍💻 ผู้พัฒนา (Author)

**Pidchanard Mueanson** — ออกแบบและพัฒนาทั้งหมด (Design, Backend, Frontend)
