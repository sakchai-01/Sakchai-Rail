# Northwind Web Application — PHP + MySQL + CRUD API + Railway PaaS

โปรเจกต์นี้จัดทำขึ้นสำหรับ Course Project: **Development and Deployment of Web App with PHP and MySQL**
รองรับการ Deploy บนแพลตฟอร์มคลาวด์ **Railway (PaaS)** อย่างสมบูรณ์แบบ พร้อมฐานข้อมูล **Northwind** จากไฟล์ `dbNorthwind.sql`

---

## 🌟 จุดเด่นและฟีเจอร์หลัก (Features)

1. **สถาปัตยกรรม CRUD ผ่าน REST-style API (`api.php`)**
   - `GET /api.php?resource=products` — ค้นหา กรอง และแสดงรายการสินค้า
   - `POST /api.php?resource=products` — เพิ่มสินค้าใหม่เข้าสู่ระบบ
   - `PUT /api.php?resource=products` — แก้ไขข้อมูลสินค้าเดิม
   - `DELETE /api.php?resource=products` — ลบสินค้าออกจากระบบ
   - `GET /api.php?resource=categories` — ดึงข้อมูลหมวดหมู่สินค้า
   - `GET /api.php?resource=suppliers` — ดึงข้อมูลผู้จัดส่งสินค้า
   - `GET /api.php?resource=stats` — สรุปข้อมูลสถิติของระบบ

2. **ความปลอดภัยและเสถียรภาพ (Security & Reliability)**
   - ใช้ **PDO Prepared Statements 100%** เพื่อป้องกันช่องโหว่ **SQL Injection**
   - มีระบบ **Validation แจ้งเตือนข้อผิดพลาด** ทั้งฝั่ง Client และ Server
   - ระบบ **Dual-Schema Adapter** ใน `db.php`: ตรวจจับและรองรับทั้งโครงสร้างตารางของอาจารย์ (`i_ProductID`, `c_ProductName` จาก `dbNorthwind.sql`) และโครงสร้างมาตรฐาน (`ProductID`, `ProductName`) ได้อย่างไร้รอยต่อ
   - ระบบ **Auto-Initialization**: หากเริ่มรันระบบบน Railway ครั้งแรกและยังไม่มีตาราง ระบบจะนำเข้าข้อมูลจาก `dbNorthwind.sql` ให้อัตโนมัติทันที

3. **หน้าตาแอปพลิเคชันระดับพรีเมียม (Modern Responsive UI)**
   - พัฒนาด้วย Bootstrap 5.3 + Custom Modern CSS สไตล์ Glassmorphism
   - แสดงสถิติภาพรวม (Total Products, Categories, Suppliers, Average Price)
   - ฟอร์มเพิ่ม/แก้ไขสินค้าพร้อมโหมด Edit ที่แยกสถานะชัดเจน
   - ป๊อปอัปแจ้งเตือน Toast Notifications แบบทันท่วงที
   - Modal ยืนยันก่อนการลบสินค้า ป้องกันการลบข้อมูลโดยไม่ตั้งใจ
   - แท็บข้อมูลผู้จัดส่ง (Suppliers Directory) พร้อมระบบค้นหาด่วน
   - แท็บหมวดหมู่สินค้า (Categories Grid)
   - แท็บสรุปขั้นตอนการ Deploy และส่งงานในตัว

4. **ความพร้อมสำหรับการ Deploy บน Railway PaaS**
   - มี `Dockerfile` (PHP 8.2 Apache + PDO MySQL) ที่รองรับ dynamic `$PORT` ของ Railway
   - มี `railway.json`, `composer.json`, `Procfile`, `.htaccess` ครบถ้วน
   - มี `health.php` สำหรับตรวจสถานะการเชื่อมต่อฐานข้อมูลและความเร็ว Latency

---

## 📁 โครงสร้างโปรเจกต์ (Project Structure)

```text
├── Dockerfile              # Docker Container สำหรับ Railway
├── Procfile                # Heroku / PaaS Web Process configuration
├── composer.json           # Runtime dependencies สำหรับ Cloud Platform
├── railway.json            # คอนฟิกการ Build และ Deploy ของ Railway
├── .dockerignore           # กำหนดไฟล์ที่ไม่ต้องส่งเข้า Image
├── .htaccess               # Apache Web Server configuration
├── .env.example            # ตัวอย่างการตั้งค่า Environment Variables
├── dbNorthwind.sql         # ไฟล์ฐานข้อมูล Northwind ของรายวิชา
├── db.php                  # การเชื่อมต่อฐานข้อมูล และระบบ Schema Adapter
├── api.php                 # RESTful CRUD API
├── health.php              # Health Check Endpoint ตรวจสอบสถานะ MySQL
├── index.php               # Single-Page Web Application UI
├── README.md               # คู่มือการใช้งานและโครงสร้างโปรเจกต์
├── DEPLOYMENT_DOCS.md      # รายงานขั้นตอนการ Deploy ละเอียด (สำหรับ Google Docs)
└── SUBMISSION_CHECKLIST.md # เช็กลิสต์ความพร้อมก่อนส่งงาน
```

---

## 🚀 ขั้นตอนการ Deploy บน Railway (Quick Guide)

1. **สร้างโปรเจกต์:** ไปที่ [https://railway.com/](https://railway.com/) แล้วกด **New Project**
2. **เพิ่มฐานข้อมูล:** เลือก **Provision MySQL**
3. **Deploy Web Service:** กด **Add Service** → เลือก **GitHub Repo** แล้วเลือก Repository นี้
4. **ตั้งค่า Environment Variable ใน Web Service:**
   ```text
   DATABASE_URL = ${{MySQL.MYSQL_PRIVATE_URL}}
   ```
5. **สร้าง Domain สาธารณะ:** ไปที่ Setting ของ Web Service → เลือก **Generate Domain**
6. **เปิดใช้งาน:** เข้าหน้าเว็บผ่าน Live URL ที่ได้รับ และทดสอบ `/health.php`

---

## 💻 การทดสอบและรันบนเครื่อง Local (Local Development)

1. นำไฟล์ทั้งโฟลเดอร์ไปวางใน `htdocs` (สำหรับ XAMPP) หรือเปิดด้วย PHP Built-in Server
2. สร้างฐานข้อมูล `db_northwind` ใน MySQL (เช่น ผ่าน phpMyAdmin หรือ MySQL Workbench)
3. นำเข้าไฟล์ `dbNorthwind.sql` เข้าสู่ฐานข้อมูล
4. ตั้งค่าตัวแปรใน `.env` หรือกำหนดใน `db.php`
5. เปิดเบราว์เซอร์เข้าที่:
   ```text
   http://localhost/northwind-crud-assignment-final/index.php
   ```
   และตรวจสถานะที่:
   ```text
   http://localhost/northwind-crud-assignment-final/health.php
   ```

---

## 📋 ข้อมูลสำหรับการส่งงาน (Submission Requirements)

- **Live Application URL:** ระบุ URL ที่ได้รับจาก Railway
- **Process Documentation:** คัดลอกเนื้อหาจากไฟล์ [DEPLOYMENT_DOCS.md](file:///c:/northwind-crud-assignment-final/DEPLOYMENT_DOCS.md) ไปใส่ใน Google Docs และแชร์ลิงก์แบบ Anyone with the link can view
- **Source Code:** บีบอัดโปรเจกต์นี้ทั้งหมด (ZIP) ขึ้น Google Drive และแชร์ลิงก์
- **Deadline:** 23/09/69 เวลา 23:59 น.
