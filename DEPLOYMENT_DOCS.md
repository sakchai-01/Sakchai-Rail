# เอกสารประกอบการส่งงาน: รายงานขั้นตอนการทำงานและการ Deploy บน Railway (PaaS)
## Course Project: Development and Deployment of Web App with PHP and MySQL

---

### ข้อมูลเบื้องต้นของโครงการ (Project Overview)
- **ชื่อโครงการ:** Northwind Product Management Web Application
- **รายวิชา:** Web Application Project (Development and Deployment of Web App with PHP and MySQL)
- **ประเภทงาน:** งานกลุ่ม / หลายบุคคล (Work Format)
- **กำหนดส่ง (Deadline):** 23/09/69 เวลา 23:59 น.

#### ลิงก์สำหรับการส่งงาน (Submission Links)
- **Live Application URL:** `[ระบุ URL ของ Web Application ที่ deploy บน Railway เช่น https://northwind-crud-production.up.railway.app]`
- **Health Check Endpoint:** `[ระบุ URL Health Check เช่น https://northwind-crud-production.up.railway.app/health.php]`
- **Google Docs Documentation:** `[วางลิงก์เอกสาร Google Docs นี้ที่มีสิทธิ์ Anyone with the link can view]`
- **Google Drive Source Code:** `[วางลิงก์โฟลเดอร์ Google Drive รวมโค้ดทั้งหมด (ZIP) ที่เปิดสิทธิ์ view]`

---

## 1. สถาปัตยกรรมและเทคโนโลยีของระบบ (Architecture & Stack)

ระบบนี้ถูกพัฒนาขึ้นตามโจทย์ที่กำหนด โดยครอบคลุมทั้ง 2 ช่วงสำคัญ ได้แก่:

### 1.1 Development Phase
1. **Backend Engine:** พัฒนาด้วยภาษา **PHP 8.2+** ตามหลัก Modern PHP Practices
2. **Database Access:** เชื่อมต่อผ่าน **PHP Data Objects (PDO)** โดยใช้ **Prepared Statements** 100% เพื่อป้องกันช่องโหว่ **SQL Injection**
3. **API & CRUD Architecture:** ออกแบบเว็บแอปพลิเคชันโดยใช้หลักการ CRUD ผ่าน RESTful-style JSON API (`api.php`):
   - `GET /api.php?resource=products` — เรียกดูรายการสินค้า (Read), ค้นหา (Search) และกรองข้อมูล (Filter)
   - `POST /api.php?resource=products` — เพิ่มสินค้าใหม่เข้าสู่ระบบ (Create)
   - `PUT /api.php?resource=products` — แก้ไขข้อมูลสินค้าเดิม (Update)
   - `DELETE /api.php?resource=products` — ลบสินค้าออกจากระบบ (Delete)
   - `GET /api.php?resource=categories` — ดึงรายชื่อหมวดหมู่สินค้า
   - `GET /api.php?resource=suppliers` — ดึงรายชื่อผู้จัดส่งสินค้า
   - `GET /api.php?resource=stats` — สรุปข้อมูลสถิติของระบบ
4. **Frontend User Interface:**
   - ออกแบบด้วย **HTML5**, **CSS3 (Modern UI)**, และ **Bootstrap 5.3**
   - รองรับ Responsive Design ทุกขนาดหน้าจอ (Desktop, Tablet, Mobile)
   - ใช้ฟอนต์ **Prompt** และ **Inter** เพื่อความสวยงามทันสมัย
   - มีระบบ **Validation แจ้งเตือนแบบ Real-time** ทั้งหน้าบ้าน (Client-side) และหลังบ้าน (Server-side)
   - มีระบบแจ้งผลการทำ CRUD สำเร็จผ่าน **Toast Notifications** และ **Confirmation Modal** สำหรับการลบข้อมูล

### 1.2 Deployment & Database Phase
1. **Cloud Platform (PaaS):** ใช้แพลตฟอร์ม **Railway ([https://railway.com/](https://railway.com/))** สำหรับ Deploy Web Application และฐานข้อมูล MySQL ขึ้นสู่อินเทอร์เน็ต
2. **Database Engine:** **MySQL 8.0** โฮสต์บน Railway
3. **Containerization:** ใช้ **Dockerfile** (`php:8.2-apache`) พร้อมเปิดใช้โมดูล `pdo_mysql` และรองรับ dynamic `$PORT` ของ Railway ผ่านสคริปต์ Entrypoint

---

## 2. โครงสร้างฐานข้อมูล Northwind (Database Setup)

โปรเจกต์นี้ใช้ฐานข้อมูล Northwind จากไฟล์ **`dbNorthwind.sql`** ที่ใช้ในการเรียน โดยระบบรองรับโครงสร้างตาราง ดังนี้:

### ตารางหลักที่ใช้งานในระบบ:
1. **`tb_products`** (ตารางข้อมูลสินค้า)
   - `i_ProductID` (INT, Primary Key, Auto Increment) — รหัสสินค้า
   - `c_ProductName` (VARCHAR) — ชื่อสินค้า
   - `i_SupplierID` (INT) — รหัสผู้จัดส่ง (Foreign Key อ้างอิง `tb_suppliers`)
   - `i_CategoryID` (INT) — รหัสหมวดหมู่ (Foreign Key อ้างอิง `tb_categories`)
   - `c_Unit` (VARCHAR) — ขนาดบรรจุภัณฑ์หรือหน่วยนับ
   - `i_Price` (FLOAT) — ราคาสินค้าต่อหน่วย

2. **`tb_categories`** (ตารางหมวดหมู่สินค้า)
   - `i_CategoryID` (INT, Primary Key) — รหัสหมวดหมู่
   - `c_CategoryName` (VARCHAR) — ชื่อหมวดหมู่
   - `c_Description` (VARCHAR) — คำอธิบายหมวดหมู่

3. **`tb_suppliers`** (ตารางผู้จัดส่งสินค้า)
   - `i_SupplierID` (INT, Primary Key) — รหัสผู้จัดส่ง
   - `c_SupplierName` (VARCHAR) — ชื่อบริษัทผู้จัดส่ง
   - `c_ContactName` (VARCHAR) — ชื่อผู้ติดต่อ
   - `c_Address`, `c_City`, `c_PostalCode`, `c_Country`, `c_Phone` — ที่อยู่และข้อมูลติดต่อ

> **หมายเหตุพิเศษด้านเสถียรภาพ:** โค้ดใน `db.php` และ `api.php` ได้รับการออกแบบให้รองรับทั้งชื่อคอลัมน์แบบมี Prefix (`i_ProductID`, `c_ProductName` จาก `dbNorthwind.sql`) และแบบไม่มี Prefix (`ProductID`, `ProductName`) โดยอัตโนมัติ พร้อมทั้งมีระบบ **Auto-Initialization** ที่จะตรวจสอบและนำเข้าข้อมูลจาก `dbNorthwind.sql` ให้อัตโนมัติเมื่อเริ่มรันระบบบน Railway เป็นครั้งแรก

---

## 3. ขั้นตอนการ Deploy บน Railway (Step-by-Step Deployment Guide)

### ขั้นตอนที่ 1: เตรียม Source Code บน GitHub
1. ทำการ Push โค้ดโปรเจกต์นี้ทั้งหมดขึ้นบน GitHub Repository (สามารถตั้งเป็น Private หรือ Public ได้)
2. ตรวจสอบว่าใน Repository มีไฟล์สำคัญครบถ้วน ได้แก่:
   - `Dockerfile`
   - `railway.json`
   - `composer.json`
   - `db.php`
   - `api.php`
   - `index.php`
   - `health.php`
   - `dbNorthwind.sql`

---

### ขั้นตอนที่ 2: สร้างโปรเจกต์และเพิ่มบริการ MySQL บน Railway
1. เข้าเว็บไซต์ [https://railway.com/](https://railway.com/) แล้วทำการ Login (แนะนำให้ Login ด้วยบัญชี GitHub)
2. คลิกปุ่ม **"+ New Project"**
3. เลือก **"Provision MySQL"**
4. Railway จะทำการสร้าง Container สำหรับฐานข้อมูล MySQL ให้โดยอัตโนมัติ
5. รอจนกระทั่งสถานะของ MySQL เปลี่ยนเป็น **Active** (สีเขียว)

---

### ขั้นตอนที่ 3: Deploy Web Service จาก GitHub Repository
1. ภายใน Project เดียวกันบน Railway ให้คลิกปุ่ม **"+ Create"** หรือ **"+ Add Service"**
2. เลือก **"GitHub Repo"**
3. ค้นหาและเลือก Repository โค้ด Northwind ของคุณ
4. Railway จะตรวจพบไฟล์ `Dockerfile` และ `railway.json` โดยอัตโนมัติ และเริ่มกระบวนการ Build Image

---

### ขั้นตอนที่ 4: การเชื่อมต่อฐานข้อมูล (Database Connection Setup)
เพื่อให้ Web Service สามารถเชื่อมต่อกับ MySQL Service บน Railway ได้:
1. คลิกที่กล่อง **Web Service** ในหน้า Dashboard ของ Railway
2. ไปที่แท็บ **"Variables"**
3. คลิก **"+ Add Variable"** หรือ **"Add Reference"**
4. กำหนดค่าตัวแปร:
   ```text
   DATABASE_URL = ${{MySQL.MYSQL_PRIVATE_URL}}
   ```
   *(หมายเหตุ: หากชื่อ MySQL Service ของคุณเป็นชื่ออื่น ให้เปลี่ยนคำว่า `MySQL` ให้ตรงกับชื่อ Service นั้น)*
5. เมื่อบันทึกค่า Railway จะทำการ Redeploy Web Service ให้อัตโนมัติ

---

### ขั้นตอนที่ 5: การสร้าง Public Domain (Live Application URL)
1. ไปที่แท็บ **"Settings"** ของ Web Service
2. เลื่อนลงมาที่หัวข้อ **"Networking"** หรือ **"Public Networking"**
3. คลิกปุ่ม **"Generate Domain"**
4. ระบบจะออก URL สาธารณะให้ เช่น `https://northwind-crud-production-xxxx.up.railway.app`
5. คัดลอก URL นี้ไว้สำหรับใช้ส่งงาน

---

### ขั้นตอนที่ 6: การตรวจสอบความพร้อมของฐานข้อมูล (Database Verification)
เนื่องจากระบบมีฟังก์ชัน **Auto-Initialization** ฝังอยู่ใน `db.php`:
1. เปิดเข้าไปที่ URL ของ Web Application ที่ได้จากขั้นตอนที่ 5
2. ระบบจะทำการตรวจสอบตาราง `tb_products` หากยังไม่มี จะทำการอ่านไฟล์ `dbNorthwind.sql` และนำเข้าข้อมูลให้ทันที
3. ทดสอบเปิด Endpoint สำหรับตรวจสอบสถานะ:
   ```text
   https://<your-railway-url>/health.php
   ```
4. จะได้รับผลลัพธ์ JSON ยืนยันสถานะ เช่น:
   ```json
   {
     "success": true,
     "status": "healthy",
     "database": "connected",
     "database_name": "railway",
     "counts": {
       "products": 77,
       "categories": 8,
       "suppliers": 29
     },
     "schema_mode": "prefixed (i_ProductID / dbNorthwind.sql)",
     "latency_ms": 1.25,
     "php_version": "8.2.xx"
   }
   ```
   *(หากแสดง `"database": "connected"` และมีตัวเลข products แสดงว่าการเชื่อมต่อและข้อมูลสมบูรณ์ 100%)*

---

## 4. ผลการทดสอบฟังก์ชันของ Web Application (Test Cases & Results)

| รายการทดสอบ (Feature) | วิธีการทดสอบ | ผลลัพธ์ที่คาดหวัง | สถานะ |
|---|---|---|:---:|
| **1. Read / Search** | พิมพ์คำค้นหา เช่น "Chai" หรือเลือกรหัสสินค้า | ตารางแสดงเฉพาะรายการที่ตรงกับการค้นหาทันที | ผ่าน (Pass) |
| **2. Filter & Sort** | เลือกหมวดหมู่ใน Dropdown หรือจัดเรียงตามราคา | รายการสินค้าถูกกรองและเรียงลำดับถูกต้อง | ผ่าน (Pass) |
| **3. Create (เพิ่มสินค้า)** | กรอกข้อมูลในฟอร์มให้ครบถ้วนแล้วกด "บันทึกสินค้า" | ข้อมูลถูกบันทึกผ่าน API (POST), แสดง Success Toast และตารางอัปเดตทันที | ผ่าน (Pass) |
| **4. Validation แจ้งเตือน** | เว้นว่างชื่อสินค้า หรือกรอกราคาติดลบแล้วกดบันทึก | แสดงขอบแดงและข้อความเตือนใต้ช่อง พร้อมแจ้งเตือน Toast สีส้ม/แดง | ผ่าน (Pass) |
| **5. Update (แก้ไขสินค้า)** | คลิกปุ่มดินสอที่แถวสินค้า ปรับปรุงข้อมูล แล้วกดยืนยัน | ข้อมูลถูกอัปเดตผ่าน API (PUT), แสดง Success Toast และข้อมูลในตารางเปลี่ยนตาม | ผ่าน (Pass) |
| **6. Delete (ลบสินค้า)** | คลิกปุ่มถังขยะ จะมี Modal ขึ้นมาถามยืนยัน เมื่อกดยืนยัน | ส่งคำสั่ง API (DELETE), ลบแถวออกจากฐานข้อมูล และแสดง Success Toast | ผ่าน (Pass) |
| **7. Suppliers Directory** | คลิกแท็บ "ข้อมูลผู้จัดส่ง" | แสดงรายการผู้จัดส่งทั้งหมด 29 รายการ พร้อมระบบค้นหา | ผ่าน (Pass) |
| **8. Health Diagnostics** | คลิกปุ่ม "Health Check" บนแถบเมนูบน | แสดงข้อมูล JSON สถานะการเชื่อมต่อ และจำนวนรายการในฐานข้อมูล | ผ่าน (Pass) |

---

## 5. สรุปรายการไฟล์สำหรับการส่งงาน (Submission Package)

1. **Live Web Application URL:**
   `https://[your-service-domain].up.railway.app`
2. **Health Check URL:**
   `https://[your-service-domain].up.railway.app/health.php`
3. **Google Docs Link:**
   ลิงก์เอกสารขั้นตอนการทำงาน (คัดลอกเนื้อหาในรายงานนี้ไปใส่ใน Google Docs)
4. **Google Drive Link:**
   ลิงก์ Source Code ที่บีบอัดเป็น `.zip` ประกอบด้วย:
   - `index.php` (หน้าจอ UI หลัก)
   - `api.php` (CRUD API Engine)
   - `db.php` (การเชื่อมต่อและจัดการฐานข้อมูล)
   - `health.php` (หน้าตรวจสุขภาพระบบ)
   - `dbNorthwind.sql` (ไฟล์ฐานข้อมูลต้นฉบับ)
   - `Dockerfile` (ไฟล์ Container สำหรับ Railway)
   - `railway.json` (ไฟล์คอนฟิกการ Deploy บน Railway)
   - `composer.json` (คอนฟิกรันไทม์ PHP)
   - `.htaccess` (คอนฟิก Apache Web Server)
   - `README.md` (คู่มือโครงการ)
   - `DEPLOYMENT_DOCS.md` (เอกสารรายงานฉบับสมบูรณ์)
