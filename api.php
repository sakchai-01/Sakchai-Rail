<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/db.php';

function jsonResponse(bool $success, string $message = '', mixed $data = [], int $status = 200, array $extra = []): never {
    http_response_code($status);
    $response = array_merge([
        'success' => $success,
        'message' => $message,
        'data'    => $data,
    ], $extra);
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function requestBody(): array {
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return !empty($_POST) ? $_POST : [];
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        jsonResponse(false, 'รูปแบบข้อมูล JSON ไม่ถูกต้อง', [], 400);
    }
    return $data;
}

function positiveInt(mixed $value, string $fieldName): int {
    if (filter_var($value, FILTER_VALIDATE_INT) === false || (int)$value < 1) {
        jsonResponse(false, "{$fieldName} ต้องเป็นตัวเลขจำนวนเต็มบวกที่มากกว่า 0", [], 422);
    }
    return (int)$value;
}

function nullableInt(mixed $value, string $fieldName): ?int {
    if ($value === null || $value === '' || $value === 'null') {
        return null;
    }
    if (filter_var($value, FILTER_VALIDATE_INT) === false || (int)$value < 1) {
        jsonResponse(false, "{$fieldName} ต้องเป็นตัวเลขจำนวนเต็มบวกหรือเว้นว่าง", [], 422);
    }
    return (int)$value;
}

function validateProductInput(array $data, bool $isUpdate = false): array {
    $errors = [];

    if ($isUpdate) {
        $productId = $data['ProductID'] ?? null;
        if (filter_var($productId, FILTER_VALIDATE_INT) === false || (int)$productId < 1) {
            $errors[] = 'รหัสสินค้า (ProductID) ไม่ถูกต้อง';
        }
    }

    $name = trim((string)($data['ProductName'] ?? ''));
    if ($name === '') {
        $errors[] = 'กรุณาระบุชื่อสินค้า (ProductName)';
    } elseif (mb_strlen($name) > 255) {
        $errors[] = 'ชื่อสินค้าต้องมีความยาวไม่เกิน 255 ตัวอักษร';
    }

    $unit = trim((string)($data['Unit'] ?? ''));
    if ($unit === '') {
        $errors[] = 'กรุณาระบุขนาดบรรจุ / หน่วยนับ (Unit)';
    } elseif (mb_strlen($unit) > 100) {
        $errors[] = 'หน่วยนับต้องมีความยาวไม่เกิน 100 ตัวอักษร';
    }

    $priceRaw = $data['Price'] ?? null;
    if ($priceRaw === null || $priceRaw === '' || !is_numeric($priceRaw)) {
        $errors[] = 'กรุณาระบุราคาสินค้าเป็นตัวเลข';
    } elseif ((float)$priceRaw < 0) {
        $errors[] = 'ราคาสินค้าต้องไม่ติดลบ (ตั้งแต่ 0 ขึ้นไป)';
    }

    $supplierId = null;
    if (isset($data['SupplierID']) && $data['SupplierID'] !== '' && $data['SupplierID'] !== null) {
        if (filter_var($data['SupplierID'], FILTER_VALIDATE_INT) === false || (int)$data['SupplierID'] < 1) {
            $errors[] = 'ผู้จัดส่ง (Supplier) ต้องเลือกจากรายการที่ถูกต้อง';
        } else {
            $supplierId = (int)$data['SupplierID'];
        }
    }

    $categoryId = null;
    if (isset($data['CategoryID']) && $data['CategoryID'] !== '' && $data['CategoryID'] !== null) {
        if (filter_var($data['CategoryID'], FILTER_VALIDATE_INT) === false || (int)$data['CategoryID'] < 1) {
            $errors[] = 'หมวดหมู่สินค้า (Category) ต้องเลือกจากรายการที่ถูกต้อง';
        } else {
            $categoryId = (int)$data['CategoryID'];
        }
    }

    if (!empty($errors)) {
        jsonResponse(false, implode(' | ', $errors), ['errors' => $errors], 422);
    }

    return [
        'name'        => $name,
        'supplier_id' => $supplierId,
        'category_id' => $categoryId,
        'unit'        => $unit,
        'price'       => round((float)$priceRaw, 2),
    ];
}

try {
    $pdo = db();
    $schema = getNorthwindSchema($pdo);
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    $resource = strtolower((string)($_GET['resource'] ?? 'products'));

    $pTable = $schema['products']['table'];
    $pId    = $schema['products']['id'];
    $pName  = $schema['products']['name'];
    $pSup   = $schema['products']['supplier_id'];
    $pCat   = $schema['products']['category_id'];
    $pUnit  = $schema['products']['unit'];
    $pPrice = $schema['products']['price'];

    $sTable   = $schema['suppliers']['table'];
    $sId      = $schema['suppliers']['id'];
    $sName    = $schema['suppliers']['name'];
    $sContact = $schema['suppliers']['contact'] ?? 'ContactName';
    $sAddress = $schema['suppliers']['address'] ?? 'Address';
    $sCity    = $schema['suppliers']['city'] ?? 'City';
    $sPostal  = $schema['suppliers']['postal_code'] ?? 'PostalCode';
    $sCountry = $schema['suppliers']['country'] ?? 'Country';
    $sPhone   = $schema['suppliers']['phone'] ?? 'Phone';

    $cTable = $schema['categories']['table'];
    $cId    = $schema['categories']['id'];
    $cName  = $schema['categories']['name'];
    $cDesc  = $schema['categories']['desc'] ?? 'Description';

    // ==========================================
    // 1. Resource: categories (GET, POST, PUT, DELETE)
    // ==========================================
    if ($resource === 'categories') {
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT {$cId} AS CategoryID, {$cName} AS CategoryName, {$cDesc} AS Description FROM {$cTable} ORDER BY {$cId} ASC");
            $rows = $stmt->fetchAll();
            jsonResponse(true, 'โหลดข้อมูลหมวดหมู่สินค้าสำเร็จ', $rows);
        }

        if ($method === 'POST') {
            $data = requestBody();
            $name = trim((string)($data['CategoryName'] ?? ''));
            $desc = trim((string)($data['Description'] ?? ''));

            if ($name === '') {
                jsonResponse(false, 'กรุณาระบุชื่อหมวดหมู่สินค้า (CategoryName)', [], 422);
            }
            if (mb_strlen($name) > 100) {
                jsonResponse(false, 'ชื่อหมวดหมู่ต้องมีความยาวไม่เกิน 100 ตัวอักษร', [], 422);
            }

            $nextId = (int)$pdo->query("SELECT COALESCE(MAX({$cId}), 0) + 1 FROM {$cTable}")->fetchColumn();

            $stmt = $pdo->prepare("INSERT INTO {$cTable} ({$cId}, {$cName}, {$cDesc}) VALUES (?, ?, ?)");
            $stmt->execute([$nextId, $name, $desc]);

            jsonResponse(true, "เพิ่มหมวดหมู่สินค้า '{$name}' (รหัส #{$nextId}) สำเร็จแล้ว", [
                'CategoryID'   => $nextId,
                'CategoryName' => $name,
                'Description'  => $desc,
            ], 201);
        }

        if ($method === 'PUT') {
            $data = requestBody();
            $id = positiveInt($data['CategoryID'] ?? null, 'CategoryID');
            $name = trim((string)($data['CategoryName'] ?? ''));
            $desc = trim((string)($data['Description'] ?? ''));

            if ($name === '') {
                jsonResponse(false, 'กรุณาระบุชื่อหมวดหมู่สินค้า', [], 422);
            }

            $checkStmt = $pdo->prepare("SELECT {$cId} FROM {$cTable} WHERE {$cId} = ?");
            $checkStmt->execute([$id]);
            if (!$checkStmt->fetch()) {
                jsonResponse(false, "ไม่พบหมวดหมู่ที่ต้องการแก้ไข (รหัส #{$id})", [], 404);
            }

            $updateStmt = $pdo->prepare("UPDATE {$cTable} SET {$cName} = ?, {$cDesc} = ? WHERE {$cId} = ?");
            $updateStmt->execute([$name, $desc, $id]);

            jsonResponse(true, "แก้ไขข้อมูลหมวดหมู่ #{$id} เรียบร้อยแล้ว", [
                'CategoryID'   => $id,
                'CategoryName' => $name,
                'Description'  => $desc,
            ]);
        }

        if ($method === 'DELETE') {
            $data = requestBody();
            $id = positiveInt($data['CategoryID'] ?? ($_GET['CategoryID'] ?? ($_GET['id'] ?? null)), 'CategoryID');

            $checkStmt = $pdo->prepare("SELECT {$cId}, {$cName} FROM {$cTable} WHERE {$cId} = ?");
            $checkStmt->execute([$id]);
            $existing = $checkStmt->fetch();
            if (!$existing) {
                jsonResponse(false, "ไม่พบหมวดหมู่ที่ต้องการลบ (รหัส #{$id})", [], 404);
            }

            $delStmt = $pdo->prepare("DELETE FROM {$cTable} WHERE {$cId} = ?");
            $delStmt->execute([$id]);

            jsonResponse(true, "ลบหมวดหมู่รหัส #{$id} เรียบร้อยแล้ว", [
                'CategoryID' => $id,
            ]);
        }

        jsonResponse(false, 'Method ไม่รองรับสำหรับ categories', [], 405);
    }

    // ==========================================
    // 2. Resource: suppliers (GET, POST, PUT, DELETE)
    // ==========================================
    if ($resource === 'suppliers') {
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT 
                {$sId} AS SupplierID, 
                {$sName} AS SupplierName, 
                {$sContact} AS ContactName, 
                {$sAddress} AS Address,
                {$sCity} AS City, 
                {$sPostal} AS PostalCode,
                {$sCountry} AS Country, 
                {$sPhone} AS Phone 
            FROM {$sTable} 
            ORDER BY {$sId} ASC");
            $rows = $stmt->fetchAll();
            jsonResponse(true, 'โหลดข้อมูลผู้จัดส่งสำเร็จ', $rows);
        }

        if ($method === 'POST') {
            $data = requestBody();
            $name    = trim((string)($data['SupplierName'] ?? ''));
            $contact = trim((string)($data['ContactName'] ?? ''));
            $address = trim((string)($data['Address'] ?? ''));
            $city    = trim((string)($data['City'] ?? ''));
            $postal  = trim((string)($data['PostalCode'] ?? ''));
            $country = trim((string)($data['Country'] ?? ''));
            $phone   = trim((string)($data['Phone'] ?? ''));

            if ($name === '') {
                jsonResponse(false, 'กรุณาระบุชื่อบริษัท / ผู้จัดส่ง (SupplierName)', [], 422);
            }

            $nextId = (int)$pdo->query("SELECT COALESCE(MAX({$sId}), 0) + 1 FROM {$sTable}")->fetchColumn();

            $stmt = $pdo->prepare("INSERT INTO {$sTable} ({$sId}, {$sName}, {$sContact}, {$sAddress}, {$sCity}, {$sPostal}, {$sCountry}, {$sPhone}) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nextId, $name, $contact, $address, $city, $postal, $country, $phone]);

            jsonResponse(true, "เพิ่มผู้จัดส่ง '{$name}' (รหัส #{$nextId}) สำเร็จแล้ว", [
                'SupplierID'   => $nextId,
                'SupplierName' => $name,
                'ContactName'  => $contact,
                'Address'      => $address,
                'City'         => $city,
                'PostalCode'   => $postal,
                'Country'      => $country,
                'Phone'        => $phone,
            ], 201);
        }

        if ($method === 'PUT') {
            $data = requestBody();
            $id = positiveInt($data['SupplierID'] ?? null, 'SupplierID');
            $name    = trim((string)($data['SupplierName'] ?? ''));
            $contact = trim((string)($data['ContactName'] ?? ''));
            $address = trim((string)($data['Address'] ?? ''));
            $city    = trim((string)($data['City'] ?? ''));
            $postal  = trim((string)($data['PostalCode'] ?? ''));
            $country = trim((string)($data['Country'] ?? ''));
            $phone   = trim((string)($data['Phone'] ?? ''));

            if ($name === '') {
                jsonResponse(false, 'กรุณาระบุชื่อบริษัทผู้จัดส่ง', [], 422);
            }

            $checkStmt = $pdo->prepare("SELECT {$sId} FROM {$sTable} WHERE {$sId} = ?");
            $checkStmt->execute([$id]);
            if (!$checkStmt->fetch()) {
                jsonResponse(false, "ไม่พบผู้จัดส่งที่ต้องการแก้ไข (รหัส #{$id})", [], 404);
            }

            $updateStmt = $pdo->prepare("UPDATE {$sTable} SET 
                {$sName} = ?, 
                {$sContact} = ?, 
                {$sAddress} = ?, 
                {$sCity} = ?, 
                {$sPostal} = ?, 
                {$sCountry} = ?, 
                {$sPhone} = ? 
            WHERE {$sId} = ?");
            $updateStmt->execute([$name, $contact, $address, $city, $postal, $country, $phone, $id]);

            jsonResponse(true, "แก้ไขข้อมูลผู้จัดส่ง #{$id} เรียบร้อยแล้ว");
        }

        if ($method === 'DELETE') {
            $data = requestBody();
            $id = positiveInt($data['SupplierID'] ?? ($_GET['SupplierID'] ?? ($_GET['id'] ?? null)), 'SupplierID');

            $checkStmt = $pdo->prepare("SELECT {$sId} FROM {$sTable} WHERE {$sId} = ?");
            $checkStmt->execute([$id]);
            if (!$checkStmt->fetch()) {
                jsonResponse(false, "ไม่พบผู้จัดส่งที่ต้องการลบ (รหัส #{$id})", [], 404);
            }

            $delStmt = $pdo->prepare("DELETE FROM {$sTable} WHERE {$sId} = ?");
            $delStmt->execute([$id]);

            jsonResponse(true, "ลบผู้จัดส่งรหัส #{$id} เรียบร้อยแล้ว", [
                'SupplierID' => $id,
            ]);
        }

        jsonResponse(false, 'Method ไม่รองรับสำหรับ suppliers', [], 405);
    }

    // ==========================================
    // 3. Resource: stats
    // ==========================================
    if ($resource === 'stats') {
        if ($method !== 'GET') {
            jsonResponse(false, 'Method ไม่รองรับสำหรับ stats', [], 405);
        }
        $pCount = (int)$pdo->query("SELECT COUNT(*) FROM {$pTable}")->fetchColumn();
        $cCount = (int)$pdo->query("SELECT COUNT(*) FROM {$cTable}")->fetchColumn();
        $sCount = (int)$pdo->query("SELECT COUNT(*) FROM {$sTable}")->fetchColumn();
        $priceStats = $pdo->query("SELECT AVG({$pPrice}) AS avg_price, MIN({$pPrice}) AS min_price, MAX({$pPrice}) AS max_price FROM {$pTable}")->fetch();

        jsonResponse(true, 'โหลดข้อมูลสถิติสำเร็จ', [
            'total_products'   => $pCount,
            'total_categories' => $cCount,
            'total_suppliers'  => $sCount,
            'avg_price'        => round((float)($priceStats['avg_price'] ?? 0), 2),
            'min_price'        => round((float)($priceStats['min_price'] ?? 0), 2),
            'max_price'        => round((float)($priceStats['max_price'] ?? 0), 2),
            'schema_prefixed'  => $schema['is_prefixed'],
        ]);
    }

    // ==========================================
    // 4. Resource: products (CRUD Main)
    // ==========================================
    if ($resource === 'products') {
        $baseSelect = "SELECT 
            p.{$pId} AS ProductID,
            p.{$pName} AS ProductName,
            p.{$pSup} AS SupplierID,
            s.{$sName} AS SupplierName,
            p.{$pCat} AS CategoryID,
            c.{$cName} AS CategoryName,
            p.{$pUnit} AS Unit,
            p.{$pPrice} AS Price
        FROM {$pTable} p
        LEFT JOIN {$sTable} s ON s.{$sId} = p.{$pSup}
        LEFT JOIN {$cTable} c ON c.{$cId} = p.{$pCat}";

        if ($method === 'GET') {
            if (!empty($_GET['id'])) {
                $id = positiveInt($_GET['id'], 'id');
                $stmt = $pdo->prepare("{$baseSelect} WHERE p.{$pId} = ?");
                $stmt->execute([$id]);
                $item = $stmt->fetch();
                if (!$item) {
                    jsonResponse(false, "ไม่พบสินค้ารหัส #{$id}", [], 404);
                }
                jsonResponse(true, 'ค้นหาสินค้าสำเร็จ', $item);
            }

            $search   = trim((string)($_GET['search'] ?? ''));
            $category = nullableInt($_GET['category'] ?? null, 'category');
            $supplier = nullableInt($_GET['supplier'] ?? null, 'supplier');
            $sort     = (string)($_GET['sort'] ?? 'id_desc');

            $whereParts = [];
            $params = [];

            if ($search !== '') {
                $whereParts[] = "(p.{$pName} LIKE ? OR CAST(p.{$pId} AS CHAR) LIKE ? OR s.{$sName} LIKE ? OR c.{$cName} LIKE ?)";
                $like = "%{$search}%";
                $params = array_merge($params, [$like, $like, $like, $like]);
            }

            if ($category !== null) {
                $whereParts[] = "p.{$pCat} = ?";
                $params[] = $category;
            }

            if ($supplier !== null) {
                $whereParts[] = "p.{$pSup} = ?";
                $params[] = $supplier;
            }

            $whereSql = !empty($whereParts) ? ' WHERE ' . implode(' AND ', $whereParts) : '';

            $orderBy = match ($sort) {
                'id_asc'     => " ORDER BY p.{$pId} ASC",
                'name_asc'   => " ORDER BY p.{$pName} ASC",
                'name_desc'  => " ORDER BY p.{$pName} DESC",
                'price_asc'  => " ORDER BY p.{$pPrice} ASC",
                'price_desc' => " ORDER BY p.{$pPrice} DESC",
                default      => " ORDER BY p.{$pId} DESC",
            };

            $sql = $baseSelect . $whereSql . $orderBy;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll();

            foreach ($products as &$prod) {
                $prod['ProductID'] = (int)$prod['ProductID'];
                $prod['SupplierID'] = $prod['SupplierID'] !== null ? (int)$prod['SupplierID'] : null;
                $prod['CategoryID'] = $prod['CategoryID'] !== null ? (int)$prod['CategoryID'] : null;
                $prod['Price'] = (float)$prod['Price'];
            }
            unset($prod);

            jsonResponse(true, 'โหลดข้อมูลสินค้าสำเร็จ', $products, 200, [
                'total' => count($products),
            ]);
        }

        if ($method === 'POST') {
            $input = validateProductInput(requestBody(), false);

            $stmt = $pdo->prepare("INSERT INTO {$pTable} ({$pName}, {$pSup}, {$pCat}, {$pUnit}, {$pPrice}) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['name'],
                $input['supplier_id'],
                $input['category_id'],
                $input['unit'],
                $input['price'],
            ]);
            $newId = (int)$pdo->lastInsertId();

            if ($newId <= 0) {
                $newId = (int)$pdo->query("SELECT MAX({$pId}) FROM {$pTable}")->fetchColumn();
            }

            $fetchStmt = $pdo->prepare("{$baseSelect} WHERE p.{$pId} = ?");
            $fetchStmt->execute([$newId]);
            $created = $fetchStmt->fetch() ?: [];

            if ($created) {
                $created['ProductID'] = (int)$created['ProductID'];
                $created['Price'] = (float)$created['Price'];
            }

            jsonResponse(true, "เพิ่มข้อมูลสินค้า '{$input['name']}' รหัส #{$newId} เรียบร้อยแล้ว", $created, 201);
        }

        if ($method === 'PUT') {
            $data = requestBody();
            $id = positiveInt($data['ProductID'] ?? null, 'ProductID');
            $input = validateProductInput($data, true);

            $checkStmt = $pdo->prepare("SELECT {$pId} FROM {$pTable} WHERE {$pId} = ?");
            $checkStmt->execute([$id]);
            if (!$checkStmt->fetch()) {
                jsonResponse(false, "ไม่พบสินค้าที่ต้องการแก้ไข (รหัส #{$id})", [], 404);
            }

            $updateStmt = $pdo->prepare("UPDATE {$pTable} SET 
                {$pName} = ?, 
                {$pSup} = ?, 
                {$pCat} = ?, 
                {$pUnit} = ?, 
                {$pPrice} = ? 
            WHERE {$pId} = ?");
            $updateStmt->execute([
                $input['name'],
                $input['supplier_id'],
                $input['category_id'],
                $input['unit'],
                $input['price'],
                $id,
            ]);

            $fetchStmt = $pdo->prepare("{$baseSelect} WHERE p.{$pId} = ?");
            $fetchStmt->execute([$id]);
            $updated = $fetchStmt->fetch() ?: [];

            if ($updated) {
                $updated['ProductID'] = (int)$updated['ProductID'];
                $updated['Price'] = (float)$updated['Price'];
            }

            jsonResponse(true, "แก้ไขข้อมูลสินค้า #{$id} ({$input['name']}) เรียบร้อยแล้ว", $updated);
        }

        if ($method === 'DELETE') {
            $data = requestBody();
            $id = positiveInt($data['ProductID'] ?? ($_GET['ProductID'] ?? ($_GET['id'] ?? null)), 'ProductID');

            $checkStmt = $pdo->prepare("SELECT {$pId}, {$pName} FROM {$pTable} WHERE {$pId} = ?");
            $checkStmt->execute([$id]);
            $existing = $checkStmt->fetch();
            if (!$existing) {
                jsonResponse(false, "ไม่พบสินค้าที่ต้องการลบ (รหัส #{$id})", [], 404);
            }

            $deletedName = $existing[$pName] ?? "รหัส #{$id}";

            $delStmt = $pdo->prepare("DELETE FROM {$pTable} WHERE {$pId} = ?");
            $delStmt->execute([$id]);

            jsonResponse(true, "ลบสินค้า '{$deletedName}' (รหัส #{$id}) ออกจากระบบเรียบร้อยแล้ว", [
                'ProductID' => $id,
                'ProductName' => $deletedName,
            ]);
        }

        jsonResponse(false, "HTTP Method '{$method}' ไม่รองรับสำหรับ Resource นี้", [], 405);
    }

    jsonResponse(false, "Resource '{$resource}' ไม่ถูกต้องหรือไม่รองรับ", [], 404);

} catch (Throwable $e) {
    error_log("API Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
    jsonResponse(false, 'เกิดข้อผิดพลาดของระบบ: ' . $e->getMessage(), [
        'file' => basename($e->getFile()),
        'line' => $e->getLine(),
    ], 500);
}
