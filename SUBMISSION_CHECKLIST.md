# รายการตรวจสอบความพร้อมก่อนส่งงาน (Submission Checklist)
## Course Project: Development and Deployment of Web App with PHP and MySQL

---

### 01. Cloud Platform (PaaS) — Railway
- [x] มีไฟล์ `Dockerfile` รองรับ dynamic `$PORT` ของ Railway และติดตั้ง `pdo_mysql`
- [x] มีไฟล์ `railway.json` กำหนด Builder และ Restart Policy
- [x] มีไฟล์ `composer.json` และ `Procfile` รองรับ Nixpacks / PaaS Fallback
- [x] สร้าง MySQL Database Service บน Railway เรียบร้อยแล้ว
- [x] เชื่อมต่อ Web Service กับ MySQL ผ่านตัวแปร `DATABASE_URL=${{MySQL.MYSQL_PRIVATE_URL}}`
- [x] สร้าง Public Domain ผ่าน Generate Domain ในหน้า Settings ของ Railway
- [x] ได้รับ **Live Application URL** ที่สามารถเปิดเข้าใช้งานได้จริงผ่านอินเทอร์เน็ต

---

### 02. Database Setup — Northwind (`dbNorthwind.sql`)
- [x] ใช้ไฟล์ฐานข้อมูล `dbNorthwind.sql` จากการเรียนในรายวิชา
- [x] โค้ดใน `db.php` รองรับชื่อคอลัมน์ของไฟล์อาจารย์ (`i_ProductID`, `c_ProductName`, `i_SupplierID`, `i_CategoryID`, `c_Unit`, `i_Price`)
- [x] โค้ดใน `db.php` มีระบบ Dual-Schema Adapter รองรับทั้งชื่อแบบมี Prefix และไม่มี Prefix
- [x] มีระบบ Auto-Initialization ตรวจสอบและนำเข้าฐานข้อมูลให้อัตโนมัติเมื่อเริ่มระบบครั้งแรก
- [x] ตรวจสอบหน้า `/health.php` พบว่าแสดงสถานะ `"status": "healthy"`, `"database": "connected"`

---

### 03. API & CRUD Architecture (`api.php`)
- [x] **GET (Read/Search):** ดึงรายการสินค้าทั้งหมด และรองรับการค้นหาตามชื่อ/รหัส/หมวดหมู่/ผู้จัดส่ง
- [x] **POST (Create):** เพิ่มสินค้าใหม่เข้าสู่ฐานข้อมูล พร้อมคืนค่า HTTP 201 Created
- [x] **PUT (Update):** แก้ไขข้อมูลสินค้าเดิมตาม ProductID
- [x] **DELETE (Delete):** ลบข้อมูลสินค้าออกจากฐานข้อมูลอย่างถูกต้อง
- [x] **Categories & Suppliers:** มี Endpoint ดึงข้อมูลหมวดหมู่และผู้จัดส่ง
- [x] **Security:** ใช้ **PDO Prepared Statements** ทุกคำสั่ง ป้องกัน SQL Injection 100%
- [x] **Format:** ตอบกลับผลลัพธ์เป็นมาตรฐาน JSON พร้อม HTTP Status Code ที่เหมาะสม

---

### 04. Web Application Features (`index.php`)
- [x] หน้าเว็บดีไซน์สวยงามระดับพรีเมียม สไตล์ Modern UI / Glassmorphism
- [x] ฟังก์ชันค้นหาสินค้าแบบ Instant Search พร้อมฟิลเตอร์กรองตามหมวดหมู่และจัดเรียงลำดับ
- [x] ฟังก์ชันเพิ่มสินค้าใหม่ (Create) พร้อมแถบเลือกหน่วยนับด่วน (Unit chips)
- [x] ฟังก์ชันแก้ไขสินค้า (Edit) มีการสลับโหมดและเติมข้อมูลเดิมในฟอร์มอัตโนมัติ
- [x] ฟังก์ชันลบสินค้า (Delete) มีหน้าต่าง Confirmation Modal ยืนยันก่อนลบทุกครั้ง
- [x] มีระบบ **Validation แจ้งเตือน** ทั้งแบบ Real-time ใต้ช่องกรอก และ Toast แจ้งเตือน
- [x] มีระบบ **Success / Error Notifications** แจ้งผลลัพธ์การทำงานของทุกคำสั่ง CRUD
- [x] มีแท็บข้อมูลผู้จัดส่ง (Suppliers Directory) พร้อมช่องค้นหา
- [x] มีแท็บหมวดหมู่สินค้า (Categories Grid)
- [x] มี Dashboard Cards สรุปตัวเลขสถิติ (จำนวนสินค้า, หมวดหมู่, ผู้จัดส่ง, ราคาเฉลี่ย)

---

### 05. Submission Package & Links (การส่งงาน)
- [ ] **Live Application URL:** ระบุ URL ที่ Deploy แล้วเสร็จ
- [ ] **Google Docs Link:** ลิงก์เอกสารอธิบายขั้นตอนการทำงานโดยละเอียด (คัดลอกเนื้อหาจาก `DEPLOYMENT_DOCS.md`)
- [ ] **Google Drive Link:** ลิงก์แชร์โฟลเดอร์หรือไฟล์ ZIP ของ Source Code ทั้งหมด
- [ ] **Deadline Check:** ตรวจสอบและส่งงานก่อนวันที่ **23/09/69 เวลา 23:59 น.**
