<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Northwind Product Management — Web App with PHP & MySQL on Railway</title>
  <meta name="description" content="ระบบจัดการสินค้า Northwind CRUD Web Application พัฒนาด้วย PHP, MySQL และ Deploy บน Railway Cloud Platform (PaaS)">
  
  <!-- Google Fonts: Prompt & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5.3.3 & Bootstrap Icons 1.11.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    :root {
      --primary-color: #4f46e5;
      --primary-hover: #4338ca;
      --primary-light: #eef2ff;
      --secondary-color: #0ea5e9;
      --success-color: #10b981;
      --danger-color: #ef4444;
      --dark-text: #1e293b;
      --muted-text: #64748b;
      --bg-surface: #ffffff;
      --bg-body: #f8fafc;
      --border-color: #e2e8f0;
      --radius-lg: 16px;
      --radius-md: 12px;
    }

    body {
      font-family: 'Prompt', 'Inter', -apple-system, sans-serif;
      background-color: var(--bg-body);
      color: var(--dark-text);
      min-height: 100vh;
    }

    /* Header & Branding */
    .top-navbar {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--border-color);
      position: sticky;
      top: 0;
      z-index: 1020;
    }

    .brand-logo-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    /* KPI Stat Cards */
    .stat-card {
      background: var(--bg-surface);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 1.25rem 1.5rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -2px rgba(0, 0, 0, 0.02);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.06);
    }
    .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.35rem;
    }

    /* Main Container Cards */
    .content-card {
      background: var(--bg-surface);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
    }

    /* Nav Pills */
    .custom-pills {
      background: #f1f5f9;
      padding: 6px;
      border-radius: var(--radius-md);
    }
    .custom-pills .nav-link {
      color: var(--muted-text);
      font-weight: 500;
      border-radius: 8px;
      padding: 10px 20px;
      transition: all 0.2s ease;
    }
    .custom-pills .nav-link.active {
      background: var(--primary-color);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }

    /* Form Styles */
    .form-label {
      font-size: 0.88rem;
      font-weight: 600;
      color: #334155;
      margin-bottom: 0.35rem;
    }
    .form-control, .form-select {
      border: 1.5px solid var(--border-color);
      border-radius: 10px;
      padding: 0.65rem 0.9rem;
      font-size: 0.93rem;
      transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3.5px rgba(79, 70, 229, 0.15);
    }
    .input-group-text {
      border: 1.5px solid var(--border-color);
      background: #f8fafc;
      color: var(--muted-text);
      border-radius: 10px 0 0 10px;
    }

    /* Table Styles */
    .table-container {
      max-height: 580px;
      overflow-y: auto;
      border-radius: var(--radius-md);
      border: 1px solid var(--border-color);
    }
    .table thead th {
      position: sticky;
      top: 0;
      z-index: 5;
      background: #f8fafc;
      font-weight: 600;
      font-size: 0.84rem;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      color: #475569;
      border-bottom: 2px solid var(--border-color);
      padding: 0.9rem 1rem;
    }
    .table tbody td {
      padding: 0.85rem 1rem;
      vertical-align: middle;
      font-size: 0.92rem;
      border-bottom: 1px solid #f1f5f9;
    }
    .product-row {
      transition: background-color 0.15s ease;
    }
    .product-row:hover {
      background-color: #f8fafc;
    }
    .product-row.editing-row {
      background-color: #f5f3ff !important;
      border-left: 4px solid var(--primary-color);
    }

    /* Buttons */
    .btn-primary-custom {
      background: var(--primary-color);
      border: none;
      color: #fff;
      font-weight: 500;
      border-radius: 10px;
      padding: 0.65rem 1.25rem;
      transition: all 0.2s ease;
    }
    .btn-primary-custom:hover {
      background: var(--primary-hover);
      color: #fff;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .unit-chip {
      font-size: 0.75rem;
      padding: 0.2rem 0.55rem;
      border-radius: 20px;
      cursor: pointer;
      background: #e2e8f0;
      color: #475569;
      transition: all 0.15s ease;
      user-select: none;
    }
    .unit-chip:hover {
      background: var(--primary-light);
      color: var(--primary-color);
    }

    /* Toast Notification System */
    .toast-container-custom {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1090;
      min-width: 320px;
      max-width: 420px;
    }

    /* Live status pulse badge */
    .pulse-dot {
      display: inline-block;
      width: 9px;
      height: 9px;
      border-radius: 50%;
      margin-right: 6px;
    }
    .pulse-dot.online {
      background: #10b981;
      box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
      animation: pulseGreen 1.8s infinite;
    }
    .pulse-dot.offline {
      background: #ef4444;
    }
    @keyframes pulseGreen {
      0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
      70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
      100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
  </style>
</head>
<body>

  <!-- Floating Toast Notifications Container -->
  <div id="toastContainer" class="toast-container-custom"></div>

  <!-- Top Navbar -->
  <header class="top-navbar py-2 px-3 px-lg-4 mb-4">
    <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-2" style="max-width: 1560px;">
      <div class="d-flex align-items-center gap-3">
        <div class="brand-logo-icon">
          <i class="bi bi-boxes"></i>
        </div>
        <div>
          <div class="d-flex align-items-center gap-2">
            <h1 class="h5 fw-bold mb-0">DB-Northwind</h1>
            
          </div>
          <p class="text-muted small mb-0">ระบบบริหารจัดการสินค้า • PHP & MySQL</p>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">
        <span id="apiStatusBadge" class="badge bg-light text-dark border px-3 py-2 rounded-pill d-flex align-items-center">
          <span class="pulse-dot online"></span>
          <span id="apiStatusText">กำลังตรวจสอบระบบ...</span>
        </span>
        <button id="openHealthBtn" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1" title="ดูสถานะ Health Check">
          <i class="bi bi-heart-pulse text-danger me-1"></i>Health Check
        </button>
      </div>
    </div>
  </header>

  <!-- Main Workspace -->
  <main class="container-fluid px-3 px-lg-4 mb-5" style="max-width: 1560px;">

    <!-- Dashboard Stat Cards -->
    <section class="row g-3 mb-4" id="statsSection">
      <div class="col-6 col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
          <div class="stat-icon bg-primary bg-opacity-10 text-primary">
            <i class="bi bi-box-seam"></i>
          </div>
          <div>
            <div class="text-muted small fw-medium">สินค้าทั้งหมด</div>
            <div class="h4 fw-bold mb-0" id="statTotalProducts">-</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
          <div class="stat-icon bg-info bg-opacity-10 text-info">
            <i class="bi bi-tags"></i>
          </div>
          <div>
            <div class="text-muted small fw-medium">หมวดหมู่สินค้า</div>
            <div class="h4 fw-bold mb-0" id="statTotalCategories">-</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
          <div class="stat-icon bg-warning bg-opacity-10 text-warning">
            <i class="bi bi-truck"></i>
          </div>
          <div>
            <div class="text-muted small fw-medium">รายชื่อผู้จัดส่ง</div>
            <div class="h4 fw-bold mb-0" id="statTotalSuppliers">-</div>
          </div>
        </div>
      </div>
      
    </section>

    <!-- Main Navigation Pills -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
      <ul class="nav custom-pills" role="tablist">
        <li class="nav-item">
          <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tabProducts" type="button" id="tabBtnProducts">
            <i class="bi bi-box-seam me-1"></i> จัดการสินค้า (CRUD)
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabSuppliers" type="button" id="tabBtnSuppliers">
            <i class="bi bi-truck me-1"></i> ข้อมูลผู้จัดส่ง (Suppliers)
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabCategories" type="button" id="tabBtnCategories">
            <i class="bi bi-tags me-1"></i> หมวดหมู่สินค้า (Categories)
          </button>
        </li>
      </ul>

      <div class="text-muted small d-none d-md-block">
        <i class="bi bi-shield-check text-success me-1"></i>PDO Prepared Statements • Input Validation Active
      </div>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content">

      <!-- ================= Tab 1: Products CRUD ================= -->
      <div class="tab-pane fade show active" id="tabProducts" role="tabpanel">
        <div class="row g-4">

          <!-- Left Column: CRUD Form -->
          <div class="col-12 col-xl-4">
            <div class="content-card p-4 sticky-top" style="top: 85px;">
              <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                  <div class="stat-icon bg-primary bg-opacity-10 text-primary" style="width: 38px; height: 38px; font-size: 1.1rem;">
                    <i class="bi bi-pencil-square" id="formHeaderIcon"></i>
                  </div>
                  <div>
                    <h2 class="h6 fw-bold mb-0" id="formHeaderTitle">เพิ่มสินค้าใหม่ (Create Product)</h2>
                    <span class="text-muted" style="font-size: 0.78rem;" id="formHeaderSubtitle">กรอกข้อมูลและบันทึกผ่าน API</span>
                  </div>
                </div>
                <span id="formModeBadge" class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1">
                  Create Mode
                </span>
              </div>

              <!-- Product Form -->
              <form id="productForm" novalidate>
                <input type="hidden" id="ProductID" value="">

                <!-- Product Name -->
                <div class="mb-3">
                  <label for="ProductName" class="form-label">ชื่อสินค้า (Product Name) <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-box"></i></span>
                    <input type="text" class="form-control" id="ProductName" placeholder="เช่น Chai, Chang, Tofu" maxlength="255" required>
                  </div>
                  <div class="invalid-feedback" id="valProductName">กรุณาระบุชื่อสินค้า</div>
                </div>

                <!-- Supplier Dropdown -->
                <div class="mb-3">
                  <label for="SupplierID" class="form-label">ผู้จัดส่ง (Supplier) <span class="text-danger">*</span></label>
                  <select class="form-select" id="SupplierID" required>
                    <option value="">-- กำลังโหลดผู้จัดส่ง... --</option>
                  </select>
                  <div class="invalid-feedback" id="valSupplierID">กรุณาเลือกผู้จัดส่งสินค้า</div>
                </div>

                <!-- Category Dropdown -->
                <div class="mb-3">
                  <label for="CategoryID" class="form-label">หมวดหมู่สินค้า (Category) <span class="text-danger">*</span></label>
                  <select class="form-select" id="CategoryID" required>
                    <option value="">-- กำลังโหลดหมวดหมู่... --</option>
                  </select>
                  <div class="invalid-feedback" id="valCategoryID">กรุณาเลือกหมวดหมู่สินค้า</div>
                </div>

                <!-- Unit & Presets -->
                <div class="mb-3">
                  <label for="Unit" class="form-label">ขนาดบรรจุ / หน่วยนับ (Unit) <span class="text-danger">*</span></label>
                  <div class="input-group mb-2">
                    <span class="input-group-text"><i class="bi bi-archive"></i></span>
                    <input type="text" class="form-control" id="Unit" placeholder="เช่น 24 - 12 oz bottles, 10 boxes" maxlength="100" required>
                  </div>
                  <div class="d-flex flex-wrap gap-1 align-items-center">
                    <span class="text-muted" style="font-size: 0.75rem;">เลือกด่วน:</span>
                    <span class="unit-chip" onclick="applyUnitPreset('bottles')">bottles</span>
                    <span class="unit-chip" onclick="applyUnitPreset('boxes')">boxes</span>
                    <span class="unit-chip" onclick="applyUnitPreset('pkgs.')">pkgs.</span>
                    <span class="unit-chip" onclick="applyUnitPreset('jars')">jars</span>
                    <span class="unit-chip" onclick="applyUnitPreset('cans')">cans</span>
                    <span class="unit-chip" onclick="applyUnitPreset('bags')">bags</span>
                    <span class="unit-chip" onclick="applyUnitPreset('tins')">tins</span>
                  </div>
                  <div class="invalid-feedback" id="valUnit">กรุณาระบุขนาดบรรจุหรือหน่วยนับ</div>
                </div>

                <!-- Price -->
                <div class="mb-4">
                  <label for="Price" class="form-label">ราคาสินค้า (Price) <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" id="Price" placeholder="0.00" step="0.01" min="0" required>
                  </div>
                  <div class="invalid-feedback" id="valPrice">กรุณาระบุราคาสินค้าที่ถูกต้อง (>= 0)</div>
                </div>

                <!-- Buttons -->
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary-custom flex-grow-1" id="saveProductBtn">
                    <i class="bi bi-plus-circle me-1" id="saveProductBtnIcon"></i>
                    <span id="saveProductBtnText">บันทึกสินค้า</span>
                  </button>
                  <button type="button" class="btn btn-outline-secondary d-none" id="cancelEditBtn" title="ยกเลิกการแก้ไข">
                    <i class="bi bi-x-circle me-1"></i>ยกเลิก
                  </button>
                  <button type="button" class="btn btn-light border text-muted" id="clearFormBtn" title="ล้างฟอร์ม">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Right Column: Products Table & Filters -->
          <div class="col-12 col-xl-8">
            <div class="content-card p-4">

              <!-- Filter & Search Toolbar -->
              <div class="row g-2 mb-3 align-items-center">
                <div class="col-12 col-md-5">
                  <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" id="searchInput" placeholder="ค้นหาชื่อสินค้า, รหัส, หมวดหมู่, หรือผู้จัดส่ง...">
                    <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn" title="ล้างการค้นหา">
                      <i class="bi bi-x-lg"></i>
                    </button>
                  </div>
                </div>

                <div class="col-6 col-md-3">
                  <select class="form-select" id="filterCategory" title="กรองตามหมวดหมู่">
                    <option value="">ทุกหมวดหมู่ (All)</option>
                  </select>
                </div>

                <div class="col-6 col-md-2">
                  <select class="form-select" id="sortBy" title="เรียงลำดับข้อมูล">
                    <option value="id_desc">ID ล่าสุด</option>
                    <option value="id_asc">ID แรกสุด</option>
                    <option value="name_asc">ชื่อ A-Z</option>
                    <option value="name_desc">ชื่อ Z-A</option>
                    <option value="price_asc">ราคา ต่ำ-สูง</option>
                    <option value="price_desc">ราคา สูง-ต่ำ</option>
                  </select>
                </div>

                <div class="col-12 col-md-2 d-flex justify-content-end gap-1">
                  <button class="btn btn-outline-primary w-100" id="refreshTableBtn" title="รีเฟรชข้อมูล">
                    <i class="bi bi-arrow-clockwise me-1"></i>รีเฟรช
                  </button>
                </div>
              </div>

              <!-- Table Information Bar -->
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2 px-1">
                <div class="small text-muted">
                  แสดง <span class="fw-bold text-dark" id="displayRange">0-0</span> จากทั้งหมด <span class="fw-bold text-dark" id="displayTotal">0</span> รายการ
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span class="small text-muted">แสดงหน้าละ:</span>
                  <select class="form-select form-select-sm" id="itemsPerPage" style="width: auto;">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="9999">ทั้งหมด</option>
                  </select>
                </div>
              </div>

              <!-- Product Data Table -->
              <div class="table-responsive table-container mb-3">
                <table class="table align-middle mb-0" id="mainProductTable">
                  <thead>
                    <tr>
                      <th style="width: 70px;">ID</th>
                      <th>ชื่อสินค้า (Product Name)</th>
                      <th>ผู้จัดส่ง (Supplier)</th>
                      <th>หมวดหมู่ (Category)</th>
                      <th>หน่วยนับ (Unit)</th>
                      <th class="text-end" style="width: 100px;">ราคา</th>
                      <th class="text-center" style="width: 110px;">การจัดการ</th>
                    </tr>
                  </thead>
                  <tbody id="productTableBody">
                    <tr>
                      <td colspan="7" class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        กำลังโหลดข้อมูลสินค้า...
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Pagination Controls -->
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2">
                <div class="small text-muted">
                  คลิกที่แถวสินค้าเพื่อทำการ <span class="badge bg-light text-dark border">แก้ไข</span> ได้ทันที
                </div>
                <nav aria-label="Pagination">
                  <ul class="pagination pagination-sm mb-0" id="paginationControls">
                    <!-- Pagination links injected by JS -->
                  </ul>
                </nav>
              </div>

            </div>
          </div>

        </div>
      </div>

      <!-- ================= Tab 2: Suppliers Directory ================= -->
      <div class="tab-pane fade" id="tabSuppliers" role="tabpanel">
        <div class="content-card p-4">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
              <h2 class="h5 fw-bold mb-1">รายชื่อผู้จัดส่งสินค้า (Suppliers)</h2>
              <p class="text-muted small mb-0">ข้อมูลจากตาราง <code>tb_suppliers</code> ในฐานข้อมูล Northwind</p>
            </div>
            <div class="d-flex align-items-center gap-2">
              <button class="btn btn-primary-custom btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                <i class="bi bi-plus-circle me-1"></i> เพิ่มผู้จัดส่งใหม่
              </button>
              <div class="input-group" style="max-width: 280px;">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0" id="supplierSearchInput" placeholder="ค้นหาชื่อผู้จัดส่ง หรือเมือง...">
              </div>
            </div>
          </div>

          <div class="table-responsive table-container">
            <table class="table align-middle mb-0">
              <thead>
                <tr>
                  <th style="width: 70px;">ID</th>
                  <th>ชื่อผู้จัดส่ง (Company)</th>
                  <th>ผู้ติดต่อ (Contact)</th>
                  <th>ที่อยู่ (Address)</th>
                  <th>เมือง (City)</th>
                  <th>ประเทศ (Country)</th>
                  <th>เบอร์โทรศัพท์ (Phone)</th>
                </tr>
              </thead>
              <tbody id="supplierTableBody">
                <tr><td colspan="7" class="text-center py-5 text-muted">กำลังโหลดข้อมูลผู้จัดส่ง...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ================= Tab 3: Categories Directory ================= -->
      <div class="tab-pane fade" id="tabCategories" role="tabpanel">
        <div class="content-card p-4">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
            <div>
              <h2 class="h5 fw-bold mb-1">หมวดหมู่สินค้าทั้งหมด (Categories)</h2>
              <p class="text-muted small mb-0">ข้อมูลหมวดหมู่สินค้าจากตาราง <code>tb_categories</code></p>
            </div>
            <button class="btn btn-primary-custom btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
              <i class="bi bi-plus-circle me-1"></i> เพิ่มหมวดหมู่ใหม่
            </button>
          </div>

          <div class="row g-3" id="categoriesCardContainer">
            <div class="col-12 text-center py-5 text-muted">กำลังโหลดข้อมูลหมวดหมู่...</div>
          </div>
        </div>
      </div>

    </div>

  </main>

  <!-- Footer -->
  <footer class="text-center text-muted small py-4 border-top bg-white">
    <div class="container">
      Northwind Web Application • Development & Deployment of Web App with PHP and MySQL • Cloud Platform (Railway PaaS)
    </div>
  </footer>

  <!-- ================= Delete Confirmation Modal ================= -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-0 pb-0">
          <div class="d-flex align-items-center gap-2">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger" style="width: 36px; height: 36px;">
              <i class="bi bi-exclamation-triangle"></i>
            </div>
            <h3 class="modal-title h5 fw-bold mb-0" id="deleteModalTitle">ยืนยันการลบสินค้า</h3>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-4">
          <p class="mb-2">คุณต้องการลบข้อมูลสินค้านี้ออกจากระบบหรือไม่?</p>
          <div class="p-3 bg-light rounded-3 border mb-3">
            <div class="text-muted small">รหัสสินค้า (Product ID): <strong id="deleteModalId" class="text-dark">-</strong></div>
            <div class="text-muted small">ชื่อสินค้า (Product Name): <strong id="deleteModalName" class="text-danger">-</strong></div>
          </div>
          <div class="text-danger small"><i class="bi bi-info-circle me-1"></i>การลบนี้จะทำการส่งคำสั่ง DELETE ไปยัง API และลบข้อมูลออกจากฐานข้อมูล MySQL อย่างถาวร</div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-danger rounded-3 px-4" id="confirmDeleteBtn">
            <i class="bi bi-trash3 me-1"></i>ยืนยันลบข้อมูล
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ================= Health Check Diagnostics Modal ================= -->
  <div class="modal fade" id="healthModal" tabindex="-1" aria-labelledby="healthModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-0 pb-0">
          <div class="d-flex align-items-center gap-2">
            <div class="stat-icon bg-success bg-opacity-10 text-success" style="width: 36px; height: 36px;">
              <i class="bi bi-activity"></i>
            </div>
            <h3 class="modal-title h5 fw-bold mb-0" id="healthModalTitle">ระบบและสถานะการเชื่อมต่อ (Diagnostics)</h3>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-3">
          <div id="healthModalContent" class="p-3 bg-light rounded-3 font-monospace small" style="white-space: pre-wrap;">กำลังตรวจสอบ...</div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
          <button type="button" class="btn btn-primary-custom btn-sm rounded-3" onclick="checkHealth()">
            <i class="bi bi-arrow-clockwise me-1"></i>ตรวจสอบอีกครั้ง
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ================= Add Supplier Modal ================= -->
  <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-0 pb-0">
          <div class="d-flex align-items-center gap-2">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning" style="width: 36px; height: 36px;">
              <i class="bi bi-truck"></i>
            </div>
            <h3 class="modal-title h5 fw-bold mb-0" id="addSupplierModalTitle">เพิ่มผู้จัดส่งสินค้าใหม่ (Add Supplier)</h3>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="addSupplierForm" novalidate>
          <div class="modal-body py-3">
            <div class="mb-3">
              <label class="form-label" for="addSupplierName">ชื่อบริษัท / ผู้จัดส่ง <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="addSupplierName" placeholder="เช่น ABC Logistics, Siam Foods" maxlength="100" required>
              <div class="invalid-feedback">กรุณาระบุชื่อผู้จัดส่งสินค้า</div>
            </div>
            <div class="mb-3">
              <label class="form-label" for="addContactName">ชื่อผู้ติดต่อ</label>
              <input type="text" class="form-control" id="addContactName" placeholder="เช่น สมชาย ใจดี" maxlength="100">
            </div>
            <div class="mb-3">
              <label class="form-label" for="addAddress">ที่อยู่</label>
              <input type="text" class="form-control" id="addAddress" placeholder="เช่น 123 ถ.สุขุมวิท" maxlength="150">
            </div>
            <div class="row g-2 mb-3">
              <div class="col-7">
                <label class="form-label" for="addCity">เมือง / จังหวัด</label>
                <input type="text" class="form-control" id="addCity" placeholder="เช่น Bangkok, Chiang Mai" maxlength="50">
              </div>
              <div class="col-5">
                <label class="form-label" for="addPostalCode">รหัสไปรษณีย์</label>
                <input type="text" class="form-control" id="addPostalCode" placeholder="เช่น 10110" maxlength="20">
              </div>
            </div>
            <div class="row g-2">
              <div class="col-6">
                <label class="form-label" for="addCountry">ประเทศ</label>
                <input type="text" class="form-control" id="addCountry" placeholder="เช่น Thailand, Japan" maxlength="50">
              </div>
              <div class="col-6">
                <label class="form-label" for="addPhone">เบอร์โทรศัพท์</label>
                <input type="text" class="form-control" id="addPhone" placeholder="เช่น (02) 123-4567" maxlength="50">
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">ยกเลิก</button>
            <button type="submit" class="btn btn-primary-custom rounded-3 px-4" id="saveSupplierBtn">
              <i class="bi bi-plus-circle me-1"></i>บันทึกผู้จัดส่ง
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ================= Add Category Modal ================= -->
  <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-0 pb-0">
          <div class="d-flex align-items-center gap-2">
            <div class="stat-icon bg-info bg-opacity-10 text-info" style="width: 36px; height: 36px;">
              <i class="bi bi-tag"></i>
            </div>
            <h3 class="modal-title h5 fw-bold mb-0" id="addCategoryModalTitle">เพิ่มหมวดหมู่สินค้าใหม่ (Add Category)</h3>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="addCategoryForm" novalidate>
          <div class="modal-body py-3">
            <div class="mb-3">
              <label class="form-label" for="addCategoryName">ชื่อหมวดหมู่สินค้า <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="addCategoryName" placeholder="เช่น Bakery, Electronics, Organic" maxlength="100" required>
              <div class="invalid-feedback">กรุณาระบุชื่อหมวดหมู่สินค้า</div>
            </div>
            <div class="mb-3">
              <label class="form-label" for="addCategoryDesc">คำอธิบายหมวดหมู่</label>
              <textarea class="form-control" id="addCategoryDesc" rows="3" placeholder="ระบุรายละเอียดเพิ่มเติมเกี่ยวกับสินค้าในหมวดหมู่นี้..." maxlength="255"></textarea>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">ยกเลิก</button>
            <button type="submit" class="btn btn-primary-custom rounded-3 px-4" id="saveCategoryBtn">
              <i class="bi bi-plus-circle me-1"></i>บันทึกหมวดหมู่
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Application Logic JavaScript -->
  <script>
    const API_BASE = 'api.php';
    let allProducts = [];
    let filteredProducts = [];
    let categoriesList = [];
    let suppliersList = [];
    let editingProductId = null;
    let pendingDeleteId = null;

    let currentPage = 1;
    let itemsPerPage = 25;

    // Bootstrap Modal instances
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const healthModal = new bootstrap.Modal(document.getElementById('healthModal'));
    const addSupplierModal = new bootstrap.Modal(document.getElementById('addSupplierModal'));
    const addCategoryModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));

    // --- Toast Notification System ---
    function showToast(message, type = 'success') {
      const container = document.getElementById('toastContainer');
      const toastId = 'toast_' + Date.now();
      
      const config = {
        success: { bg: 'bg-success text-white', icon: 'bi-check-circle-fill', title: 'สำเร็จ' },
        danger:  { bg: 'bg-danger text-white',  icon: 'bi-exclamation-triangle-fill', title: 'ข้อผิดพลาด' },
        warning: { bg: 'bg-warning text-dark', icon: 'bi-exclamation-circle-fill', title: 'แจ้งเตือน' },
        info:    { bg: 'bg-primary text-white', icon: 'bi-info-circle-fill', title: 'ข้อมูล' }
      }[type] || { bg: 'bg-dark text-white', icon: 'bi-bell-fill', title: 'แจ้งเตือน' };

      const toastHtml = `
        <div id="${toastId}" class="toast align-items-center ${config.bg} border-0 shadow-lg mb-2" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2 py-2 px-3">
              <i class="bi ${config.icon} fs-5"></i>
              <div>
                <div class="fw-bold" style="font-size: 0.82rem;">${config.title}</div>
                <div style="font-size: 0.88rem;">${escapeHtml(message)}</div>
              </div>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      `;

      container.insertAdjacentHTML('beforeend', toastHtml);
      const toastEl = document.getElementById(toastId);
      const bsToast = new bootstrap.Toast(toastEl, { delay: 4500 });
      bsToast.show();
      toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }

    function escapeHtml(text) {
      if (text === null || text === undefined) return '';
      return String(text).replace(/[&<>'"]/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        "'": '&#039;',
        '"': '&quot;'
      }[char]));
    }

    // --- API Request Wrapper ---
    async function apiRequest(url, options = {}) {
      try {
        const response = await fetch(url, {
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          ...options
        });

        const text = await response.text();
        let json;
        try {
          json = JSON.parse(text);
        } catch (e) {
          throw new Error(`API ไม่ตอบกลับเป็น JSON (HTTP ${response.status})`);
        }

        if (!response.ok || !json.success) {
          throw new Error(json.message || `เกิดข้อผิดพลาด (HTTP ${response.status})`);
        }

        return json;
      } catch (err) {
        throw err;
      }
    }

    // --- Load Metadata (Categories & Suppliers & Stats) ---
    async function loadMetadata() {
      try {
        const [catsRes, supsRes, statsRes] = await Promise.all([
          apiRequest(`${API_BASE}?resource=categories`),
          apiRequest(`${API_BASE}?resource=suppliers`),
          apiRequest(`${API_BASE}?resource=stats`)
        ]);

        categoriesList = catsRes.data || [];
        suppliersList  = supsRes.data || [];

        // 1. Populate Form Dropdowns
        populateSelect('CategoryID', categoriesList, 'CategoryID', 'CategoryName', '-- เลือกหมวดหมู่สินค้า --');
        populateSelect('SupplierID', suppliersList, 'SupplierID', 'SupplierName', '-- เลือกผู้จัดส่งสินค้า --');

        // 2. Populate Filter Dropdown
        populateSelect('filterCategory', categoriesList, 'CategoryID', 'CategoryName', 'ทุกหมวดหมู่ (All)');

        // 3. Render Suppliers Tab Table
        renderSuppliersTab(suppliersList);

        // 4. Render Categories Tab Grid
        renderCategoriesTab(categoriesList);

        // 5. Update KPI Stats
        if (statsRes.data) {
          document.getElementById('statTotalProducts').textContent = statsRes.data.total_products.toLocaleString();
          document.getElementById('statTotalCategories').textContent = statsRes.data.total_categories.toLocaleString();
          document.getElementById('statTotalSuppliers').textContent = statsRes.data.total_suppliers.toLocaleString();
          document.getElementById('statAvgPrice').textContent = '$' + Number(statsRes.data.avg_price).toFixed(2);
        }

        updateApiStatus(true, 'API Connected • DB Online');
      } catch (err) {
        updateApiStatus(false, 'API Disconnected');
        showToast('ไม่สามารถโหลดข้อมูลเริ่มต้นได้: ' + err.message, 'danger');
      }
    }

    function populateSelect(selectId, items, valueKey, labelKey, defaultText) {
      const select = document.getElementById(selectId);
      if (!select) return;
      let html = `<option value="">${defaultText}</option>`;
      items.forEach(item => {
        html += `<option value="${item[valueKey]}">${escapeHtml(item[labelKey])}</option>`;
      });
      select.innerHTML = html;
    }

    function renderSuppliersTab(items) {
      const tbody = document.getElementById('supplierTableBody');
      if (!items || items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">ไม่พบข้อมูลผู้จัดส่ง</td></tr>';
        return;
      }
      tbody.innerHTML = items.map(s => `
        <tr>
          <td><span class="badge bg-light text-dark border">#${s.SupplierID}</span></td>
          <td class="fw-semibold text-primary">${escapeHtml(s.SupplierName)}</td>
          <td>${escapeHtml(s.ContactName || '-')}</td>
          <td>${escapeHtml(s.Address || '-')}</td>
          <td>${escapeHtml(s.City || '-')}</td>
          <td>${escapeHtml(s.Country || '-')}</td>
          <td>${escapeHtml(s.Phone || '-')}</td>
        </tr>
      `).join('');
    }

    function renderCategoriesTab(items) {
      const container = document.getElementById('categoriesCardContainer');
      if (!items || items.length === 0) {
        container.innerHTML = '<div class="col-12 text-center py-4 text-muted">ไม่พบข้อมูลหมวดหมู่</div>';
        return;
      }
      container.innerHTML = items.map(c => `
        <div class="col-12 col-md-4 col-xl-3">
          <div class="p-3 bg-light rounded-3 border h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill">ID: ${c.CategoryID}</span>
              <i class="bi bi-tag text-muted"></i>
            </div>
            <h4 class="h6 fw-bold mb-1">${escapeHtml(c.CategoryName)}</h4>
            <p class="text-muted small mb-0">${escapeHtml(c.Description || 'ไม่มีคำอธิบายเพิ่มเติม')}</p>
          </div>
        </div>
      `).join('');
    }

    // --- Load Products Data ---
    async function loadProducts() {
      const tbody = document.getElementById('productTableBody');
      tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>กำลังโหลดข้อมูลสินค้า...</td></tr>`;

      try {
        const search = document.getElementById('searchInput').value.trim();
        const cat = document.getElementById('filterCategory').value;
        const sort = document.getElementById('sortBy').value;

        let query = `?resource=products`;
        if (search) query += `&search=${encodeURIComponent(search)}`;
        if (cat) query += `&category=${encodeURIComponent(cat)}`;
        if (sort) query += `&sort=${encodeURIComponent(sort)}`;

        const res = await apiRequest(`${API_BASE}${query}`);
        allProducts = res.data || [];
        applyPaginationAndRender();
        updateApiStatus(true, 'API Connected • DB Online');
      } catch (err) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>${escapeHtml(err.message)}</td></tr>`;
        updateApiStatus(false, 'API Error');
        showToast(err.message, 'danger');
      }
    }

    // --- Client-side Pagination & Rendering ---
    function applyPaginationAndRender() {
      const total = allProducts.length;
      document.getElementById('displayTotal').textContent = total.toLocaleString();

      if (total === 0) {
        document.getElementById('productTableBody').innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>ไม่พบข้อมูลสินค้าที่ค้นหา</td></tr>`;
        document.getElementById('displayRange').textContent = '0-0';
        document.getElementById('paginationControls').innerHTML = '';
        return;
      }

      const totalPages = Math.ceil(total / itemsPerPage);
      if (currentPage > totalPages) currentPage = totalPages;
      if (currentPage < 1) currentPage = 1;

      const startIndex = (currentPage - 1) * itemsPerPage;
      const endIndex = Math.min(startIndex + itemsPerPage, total);
      document.getElementById('displayRange').textContent = `${startIndex + 1}-${endIndex}`;

      const pageItems = allProducts.slice(startIndex, endIndex);

      const tbody = document.getElementById('productTableBody');
      tbody.innerHTML = pageItems.map(p => {
        const isEditing = editingProductId === p.ProductID;
        return `
          <tr class="product-row ${isEditing ? 'editing-row' : ''}" id="prodRow_${p.ProductID}">
            <td><span class="badge bg-light text-dark border">#${p.ProductID}</span></td>
            <td>
              <div class="fw-semibold text-dark">${escapeHtml(p.ProductName)}</div>
              ${isEditing ? '<span class="badge bg-primary" style="font-size:0.68rem;">กำลังแก้ไข</span>' : ''}
            </td>
            <td><span class="text-secondary small"><i class="bi bi-truck me-1"></i>${escapeHtml(p.SupplierName || '-')}</span></td>
            <td><span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2">${escapeHtml(p.CategoryName || '-')}</span></td>
            <td><span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.8rem;">${escapeHtml(p.Unit || '-')}</span></td>
            <td class="text-end fw-bold text-dark">$${Number(p.Price || 0).toFixed(2)}</td>
            <td class="text-center text-nowrap">
              <button class="btn btn-sm btn-outline-primary rounded-2 px-2 me-1" onclick='startEditProduct(${JSON.stringify(p).replace(/'/g, "&#039;")})' title="แก้ไขสินค้า">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger rounded-2 px-2" onclick='askDeleteProduct(${p.ProductID}, ${JSON.stringify(p.ProductName).replace(/'/g, "&#039;")})' title="ลบสินค้า">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        `;
      }).join('');

      renderPaginationControls(totalPages);
    }

    function renderPaginationControls(totalPages) {
      const container = document.getElementById('paginationControls');
      if (totalPages <= 1) {
        container.innerHTML = '';
        return;
      }

      let html = '';
      html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="javascript:void(0)" onclick="goToPage(${currentPage - 1})"><i class="bi bi-chevron-left"></i></a>
      </li>`;

      for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
          html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="goToPage(${i})">${i}</a>
          </li>`;
        } else if (i === currentPage - 3 || i === currentPage + 3) {
          html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
      }

      html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="javascript:void(0)" onclick="goToPage(${currentPage + 1})"><i class="bi bi-chevron-right"></i></a>
      </li>`;

      container.innerHTML = html;
    }

    function goToPage(page) {
      currentPage = page;
      applyPaginationAndRender();
      window.scrollTo({ top: 200, behavior: 'smooth' });
    }

    // --- Form Operations (Create & Update) ---
    const form = document.getElementById('productForm');

    form.addEventListener('submit', async function(e) {
      e.preventDefault();

      // Reset validation states
      clearValidationErrors();

      const nameVal = document.getElementById('ProductName').value.trim();
      const supVal  = document.getElementById('SupplierID').value;
      const catVal  = document.getElementById('CategoryID').value;
      const unitVal = document.getElementById('Unit').value.trim();
      const priceVal = document.getElementById('Price').value;

      let hasError = false;

      // Validation Rules
      if (!nameVal) {
        setFieldError('ProductName', 'กรุณาระบุชื่อสินค้า');
        hasError = true;
      }
      if (!supVal) {
        setFieldError('SupplierID', 'กรุณาเลือกผู้จัดส่งสินค้า');
        hasError = true;
      }
      if (!catVal) {
        setFieldError('CategoryID', 'กรุณาเลือกหมวดหมู่สินค้า');
        hasError = true;
      }
      if (!unitVal) {
        setFieldError('Unit', 'กรุณาระบุขนาดบรรจุ / หน่วยนับ');
        hasError = true;
      }
      if (priceVal === '' || isNaN(priceVal) || Number(priceVal) < 0) {
        setFieldError('Price', 'กรุณาระบุราคาสินค้าเป็นตัวเลขที่มากกว่าหรือเท่ากับ 0');
        hasError = true;
      }

      if (hasError) {
        showToast('กรุณากรอกข้อมูลในฟอร์มให้ถูกต้องและครบถ้วน', 'warning');
        return;
      }

      // Payload
      const payload = {
        ProductName: nameVal,
        SupplierID: supVal ? Number(supVal) : null,
        CategoryID: catVal ? Number(catVal) : null,
        Unit: unitVal,
        Price: Number(priceVal)
      };

      const saveBtn = document.getElementById('saveProductBtn');
      saveBtn.disabled = true;
      saveBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>กำลังบันทึก...`;

      try {
        if (editingProductId) {
          // PUT request
          payload.ProductID = editingProductId;
          const res = await apiRequest(`${API_BASE}?resource=products`, {
            method: 'PUT',
            body: JSON.stringify(payload)
          });
          showToast(res.message || 'แก้ไขข้อมูลสินค้าเรียบร้อยแล้ว', 'success');
        } else {
          // POST request
          const res = await apiRequest(`${API_BASE}?resource=products`, {
            method: 'POST',
            body: JSON.stringify(payload)
          });
          showToast(res.message || 'เพิ่มสินค้าใหม่เรียบร้อยแล้ว', 'success');
        }

        resetForm();
        await loadMetadata();
        await loadProducts();
      } catch (err) {
        showToast('บันทึกข้อมูลล้มเหลว: ' + err.message, 'danger');
      } finally {
        saveBtn.disabled = false;
        updateSaveBtnLabel();
      }
    });

    function setFieldError(fieldId, errorMsg) {
      const field = document.getElementById(fieldId);
      field.classList.add('is-invalid');
      const feedback = document.getElementById('val' + fieldId);
      if (feedback) feedback.textContent = errorMsg;
    }

    function clearValidationErrors() {
      ['ProductName', 'SupplierID', 'CategoryID', 'Unit', 'Price'].forEach(id => {
        const field = document.getElementById(id);
        if (field) field.classList.remove('is-invalid');
      });
    }

    function startEditProduct(product) {
      editingProductId = Number(product.ProductID);
      document.getElementById('ProductID').value = editingProductId;
      document.getElementById('ProductName').value = product.ProductName || '';
      document.getElementById('SupplierID').value = product.SupplierID || '';
      document.getElementById('CategoryID').value = product.CategoryID || '';
      document.getElementById('Unit').value = product.Unit || '';
      document.getElementById('Price').value = product.Price !== null ? product.Price : '';

      clearValidationErrors();

      // UI updates for edit mode
      document.getElementById('formHeaderTitle').textContent = `แก้ไขสินค้า #${editingProductId}`;
      document.getElementById('formHeaderSubtitle').textContent = 'กำลังแก้ไขข้อมูลสินค้าเดิมในฐานข้อมูล';
      document.getElementById('formHeaderIcon').className = 'bi bi-pencil-fill text-primary';
      document.getElementById('formModeBadge').textContent = `Editing #${editingProductId}`;
      document.getElementById('formModeBadge').className = 'badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-1';
      document.getElementById('cancelEditBtn').classList.remove('d-none');

      updateSaveBtnLabel();

      // Switch to products tab if not on it
      document.getElementById('tabBtnProducts').click();
      window.scrollTo({ top: 120, behavior: 'smooth' });

      // Highlight row
      applyPaginationAndRender();
    }

    function resetForm() {
      editingProductId = null;
      form.reset();
      document.getElementById('ProductID').value = '';
      clearValidationErrors();

      document.getElementById('formHeaderTitle').textContent = 'เพิ่มสินค้าใหม่ (Create Product)';
      document.getElementById('formHeaderSubtitle').textContent = 'กรอกข้อมูลและบันทึกผ่าน API';
      document.getElementById('formHeaderIcon').className = 'bi bi-pencil-square text-primary';
      document.getElementById('formModeBadge').textContent = 'Create Mode';
      document.getElementById('formModeBadge').className = 'badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1';
      document.getElementById('cancelEditBtn').classList.add('d-none');

      updateSaveBtnLabel();
      applyPaginationAndRender();
    }

    function updateSaveBtnLabel() {
      const btnText = document.getElementById('saveProductBtnText');
      const btnIcon = document.getElementById('saveProductBtnIcon');
      if (editingProductId) {
        btnText.textContent = 'บันทึกการแก้ไข';
        btnIcon.className = 'bi bi-check2-circle me-1';
      } else {
        btnText.textContent = 'เพิ่มสินค้าใหม่';
        btnIcon.className = 'bi bi-plus-circle me-1';
      }
    }

    function applyUnitPreset(unit) {
      const input = document.getElementById('Unit');
      const cur = input.value.trim();
      input.value = cur ? `${cur} ${unit}` : unit;
      input.focus();
    }

    // --- Delete Operations ---
    function askDeleteProduct(id, name) {
      pendingDeleteId = Number(id);
      document.getElementById('deleteModalId').textContent = '#' + id;
      document.getElementById('deleteModalName').textContent = name;
      deleteModal.show();
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
      if (!pendingDeleteId) return;
      const btn = this;
      btn.disabled = true;
      btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>กำลังลบ...`;

      try {
        const res = await apiRequest(`${API_BASE}?resource=products`, {
          method: 'DELETE',
          body: JSON.stringify({ ProductID: pendingDeleteId })
        });

        deleteModal.hide();
        showToast(res.message || 'ลบข้อมูลสินค้าสำเร็จแล้ว', 'success');

        if (editingProductId === pendingDeleteId) {
          resetForm();
        }

        pendingDeleteId = null;
        await loadMetadata();
        await loadProducts();
      } catch (err) {
        deleteModal.hide();
        showToast('ลบสินค้าไม่สำเร็จ: ' + err.message, 'danger');
      } finally {
        btn.disabled = false;
        btn.innerHTML = `<i class="bi bi-trash3 me-1"></i>ยืนยันลบข้อมูล`;
      }
    });

    // --- Health Check Diagnostics ---
    async function checkHealth() {
      const modalContent = document.getElementById('healthModalContent');
      modalContent.textContent = 'กำลังส่งคำขอตรวจสอบไปยัง /health.php ...';
      healthModal.show();

      try {
        const res = await fetch('health.php');
        const data = await res.json();
        modalContent.textContent = JSON.stringify(data, null, 2);
        if (data.success) {
          updateApiStatus(true, 'Health: ' + data.status + ' (' + data.database_name + ')');
        } else {
          updateApiStatus(false, 'Health Check: ' + data.status);
        }
      } catch (e) {
        modalContent.textContent = 'เกิดข้อผิดพลาดในการเรียก health.php:\n' + e.message;
        updateApiStatus(false, 'Health Check Failed');
      }
    }

    function updateApiStatus(isOnline, text) {
      const badge = document.getElementById('apiStatusBadge');
      const dot = badge.querySelector('.pulse-dot');
      const statusText = document.getElementById('apiStatusText');
      if (isOnline) {
        dot.className = 'pulse-dot online';
        statusText.textContent = text;
      } else {
        dot.className = 'pulse-dot offline';
        statusText.textContent = text;
      }
    }

    // --- Search & Filters Event Listeners ---
    let searchDebounceTimeout = null;
    document.getElementById('searchInput').addEventListener('input', function() {
      clearTimeout(searchDebounceTimeout);
      searchDebounceTimeout = setTimeout(() => {
        currentPage = 1;
        loadProducts();
      }, 350);
    });

    document.getElementById('clearSearchBtn').addEventListener('click', function() {
      document.getElementById('searchInput').value = '';
      currentPage = 1;
      loadProducts();
    });

    document.getElementById('filterCategory').addEventListener('change', function() {
      currentPage = 1;
      loadProducts();
    });

    document.getElementById('sortBy').addEventListener('change', function() {
      currentPage = 1;
      loadProducts();
    });

    document.getElementById('itemsPerPage').addEventListener('change', function() {
      itemsPerPage = Number(this.value);
      currentPage = 1;
      applyPaginationAndRender();
    });

    document.getElementById('refreshTableBtn').addEventListener('click', async function() {
      const btn = this;
      btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>รีเฟรช...`;
      btn.disabled = true;
      await loadMetadata();
      await loadProducts();
      btn.innerHTML = `<i class="bi bi-arrow-clockwise me-1"></i>รีเฟรช`;
      btn.disabled = false;
      showToast('รีเฟรชข้อมูลเรียบร้อยแล้ว', 'info');
    });

    document.getElementById('cancelEditBtn').addEventListener('click', resetForm);
    document.getElementById('clearFormBtn').addEventListener('click', resetForm);
    document.getElementById('openHealthBtn').addEventListener('click', checkHealth);

    // Filter suppliers in tab 2
    document.getElementById('supplierSearchInput').addEventListener('input', function() {
      const q = this.value.toLowerCase().trim();
      const filtered = suppliersList.filter(s => 
        (s.SupplierName && s.SupplierName.toLowerCase().includes(q)) ||
        (s.City && s.City.toLowerCase().includes(q)) ||
        (s.Country && s.Country.toLowerCase().includes(q))
      );
      renderSuppliersTab(filtered);
    });

    // --- Add Supplier Form Handler ---
    document.getElementById('addSupplierForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const nameInput = document.getElementById('addSupplierName');
      const name = nameInput.value.trim();

      if (!name) {
        nameInput.classList.add('is-invalid');
        return;
      }
      nameInput.classList.remove('is-invalid');

      const payload = {
        SupplierName: name,
        ContactName: document.getElementById('addContactName').value.trim(),
        Address: document.getElementById('addAddress').value.trim(),
        City: document.getElementById('addCity').value.trim(),
        PostalCode: document.getElementById('addPostalCode').value.trim(),
        Country: document.getElementById('addCountry').value.trim(),
        Phone: document.getElementById('addPhone').value.trim(),
      };

      const btn = document.getElementById('saveSupplierBtn');
      btn.disabled = true;
      btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>กำลังบันทึก...`;

      try {
        const res = await apiRequest(`${API_BASE}?resource=suppliers`, {
          method: 'POST',
          body: JSON.stringify(payload)
        });
        addSupplierModal.hide();
        this.reset();
        showToast(res.message || 'เพิ่มผู้จัดส่งสินค้าสำเร็จ', 'success');
        await loadMetadata();
      } catch (err) {
        showToast('เพิ่มผู้จัดส่งไม่สำเร็จ: ' + err.message, 'danger');
      } finally {
        btn.disabled = false;
        btn.innerHTML = `<i class="bi bi-plus-circle me-1"></i>บันทึกผู้จัดส่ง`;
      }
    });

    // --- Add Category Form Handler ---
    document.getElementById('addCategoryForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const nameInput = document.getElementById('addCategoryName');
      const name = nameInput.value.trim();

      if (!name) {
        nameInput.classList.add('is-invalid');
        return;
      }
      nameInput.classList.remove('is-invalid');

      const payload = {
        CategoryName: name,
        Description: document.getElementById('addCategoryDesc').value.trim(),
      };

      const btn = document.getElementById('saveCategoryBtn');
      btn.disabled = true;
      btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>กำลังบันทึก...`;

      try {
        const res = await apiRequest(`${API_BASE}?resource=categories`, {
          method: 'POST',
          body: JSON.stringify(payload)
        });
        addCategoryModal.hide();
        this.reset();
        showToast(res.message || 'เพิ่มหมวดหมู่สินค้าสำเร็จ', 'success');
        await loadMetadata();
      } catch (err) {
        showToast('เพิ่มหมวดหมู่ไม่สำเร็จ: ' + err.message, 'danger');
      } finally {
        btn.disabled = false;
        btn.innerHTML = `<i class="bi bi-plus-circle me-1"></i>บันทึกหมวดหมู่`;
      }
    });

    // --- Initialization on page load ---
    document.addEventListener('DOMContentLoaded', async () => {
      await loadMetadata();
      await loadProducts();
    });
  </script>
</body>
</html>
