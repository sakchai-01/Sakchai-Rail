<?php
declare(strict_types=1);

/**
 * Database Connection & Schema Management for Northwind Application
 * Compatible with Railway MySQL PaaS and Local Development (XAMPP / Docker / CLI)
 */

function envVal(string $key, ?string $default = null): ?string {
    $v = getenv($key);
    if ($v === false || $v === '') {
        $v = $_SERVER[$key] ?? ($_ENV[$key] ?? null);
    }
    if ($v !== null && $v !== '') {
        return trim((string)$v, " \t\n\r\0\x0B\"'");
    }
    return $default;
}

function getDbConfig(): array {
    // 1. ตรวจสอบ Connection URL จาก Railway (DATABASE_URL, MYSQL_URL, MYSQL_PRIVATE_URL, MYSQL_PUBLIC_URL)
    $dbUrl = envVal('DATABASE_URL') 
          ?: envVal('MYSQL_URL') 
          ?: envVal('MYSQL_PRIVATE_URL') 
          ?: envVal('MYSQL_PUBLIC_URL')
          ?: '';

    if ($dbUrl !== '' && !str_starts_with($dbUrl, '${{')) {
        $opts = parse_url($dbUrl);
        $host = $opts['host'] ?? '';
        if ($host !== '') {
            $port = (int)($opts['port'] ?? 3306);
            $user = $opts['user'] ?? 'root';
            $pass = $opts['pass'] ?? '';
            $pathDb = isset($opts['path']) ? ltrim($opts['path'], '/') : '';
            $db = envVal('DB_NAME') ?: envVal('MYSQLDATABASE') ?: ($pathDb !== '' ? $pathDb : 'railway');

            return [
                'host' => $host,
                'port' => $port,
                'user' => $user,
                'pass' => $pass,
                'db'   => $db,
                'is_url' => true,
            ];
        }
    }

    // 2. ตรวจสอบจากตัวแปรแยก (เช่น MYSQLHOST จาก Railway หรือ .env)
    $host = envVal('MYSQLHOST') ?: envVal('DB_HOST') ?: '';
    if ($host !== '' && $host !== '127.0.0.1') {
        $port = (int)(envVal('MYSQLPORT') ?: envVal('DB_PORT') ?: 3306);
        $user = envVal('MYSQLUSER') ?: envVal('DB_USER') ?: 'root';
        $pass = envVal('MYSQLPASSWORD') ?: envVal('DB_PASS') ?: '';
        $db   = envVal('MYSQLDATABASE') ?: envVal('DB_NAME') ?: 'railway';

        return [
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'pass' => $pass,
            'db'   => $db,
            'is_url' => false,
        ];
    }

    // 3. Fallback อัตโนมัติ: เชื่อมต่อ Railway MySQL Service ตามค่าจริง
    return [
        'host' => 'mysql.railway.internal',
        'port' => 3306,
        'user' => 'root',
        'pass' => 'bBYdnFEnCYOIoEIvrXdmNlBGfVpyHpBS',
        'db'   => 'railway',
        'is_url' => true,
    ];
}

function db(): PDO {
    static $pdoInstance = null;
    if ($pdoInstance !== null) {
        return $pdoInstance;
    }

    $cfg = getDbConfig();
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        PDO::ATTR_TIMEOUT => 5,
    ];

    $pdo = null;

    // ลองเชื่อมต่อฐานข้อมูลตามชื่อที่ระบุ
    try {
        $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['db']};charset=utf8mb4";
        $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], $options);
    } catch (PDOException $e) {
        // หากเกิดข้อผิดพลาดฐานข้อมูลไม่พบ (Unknown database) หรือ 1049
        $isUnknownDb = str_contains($e->getMessage(), 'Unknown database') || $e->getCode() == 1049;

        if ($isUnknownDb) {
            // เชื่อมต่อไปที่ MySQL Server โดยไม่ระบุ dbname เพื่อสร้างฐานข้อมูล
            $dsnNoDb = "mysql:host={$cfg['host']};port={$cfg['port']};charset=utf8mb4";
            $pdoServer = new PDO($dsnNoDb, $cfg['user'], $cfg['pass'], $options);

            try {
                $targetDb = preg_replace('/[^a-zA-Z0-9_]/', '', $cfg['db']) ?: 'db_northwind';
                $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `{$targetDb}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdoServer->exec("USE `{$targetDb}`");
                $pdo = $pdoServer;
                $cfg['db'] = $targetDb;
            } catch (Throwable $e2) {
                // หากสร้าง db ไม่สำเร็จ ให้ fallback ใช้ 'railway' (ฐานข้อมูลตั้งต้นของ Railway)
                try {
                    $pdoServer->exec("USE `railway`");
                    $pdo = $pdoServer;
                    $cfg['db'] = 'railway';
                } catch (Throwable $e3) {
                    throw new RuntimeException("ไม่สามารถเชื่อมต่อหรือสร้างฐานข้อมูลได้: " . $e->getMessage(), (int)$e->getCode(), $e);
                }
            }
        } else {
            throw new RuntimeException("เชื่อมต่อฐานข้อมูลล้มเหลว ({$cfg['host']}:{$cfg['port']}): " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    // ตรวจสอบและเตรียมโครงสร้างตาราง Northwind อัตโนมัติ (Auto-Initialization)
    ensureNorthwindTables($pdo);

    $pdoInstance = $pdo;
    return $pdoInstance;
}

/**
 * ตรวจสอบความพร้อมของตาราง Northwind
 * หากยังไม่มีตาราง จะนำเข้าจาก dbNorthwind.sql โดยอัตโนมัติ
 */
function ensureNorthwindTables(PDO $pdo): void {
    try {
        $check = $pdo->query("SHOW TABLES LIKE 'tb_products'")->fetchColumn();
        if ($check) {
            upgradeNorthwindColumns($pdo);
            return;
        }

        // หากยังไม่มีตาราง ให้ตรวจสอบไฟล์ dbNorthwind.sql ในโฟลเดอร์เดียวกัน
        $sqlPath = __DIR__ . '/dbNorthwind.sql';
        if (file_exists($sqlPath)) {
            importSqlDump($pdo, $sqlPath);
            upgradeNorthwindColumns($pdo);
            return;
        }

        // Fallback: หากหาไฟล์ไม่เจอ ให้สร้าง Schema พื้นฐานเพื่อไม่ให้ระบบพัง
        createFallbackTables($pdo);
    } catch (Throwable $e) {
        error_log("Warning in ensureNorthwindTables: " . $e->getMessage());
    }
}

/**
 * ปรับปรุงขนาดคอลัมน์ในตารางให้รองรับข้อมูลจริง
 * ป้องกันข้อผิดพลาด SQLSTATE[22001]: 1406 Data too long for column
 */
function upgradeNorthwindColumns(PDO $pdo): void {
    static $upgraded = false;
    if ($upgraded) return;

    try {
        // 1. ตาราง tb_suppliers
        $colSup = $pdo->query("SHOW COLUMNS FROM `tb_suppliers` LIKE 'c_SupplierName'")->fetch(PDO::FETCH_ASSOC);
        if ($colSup && stripos($colSup['Type'], 'varchar(30)') !== false) {
            $pdo->exec("ALTER TABLE `tb_suppliers` 
                MODIFY `c_SupplierName` VARCHAR(255) NOT NULL,
                MODIFY `c_ContactName` VARCHAR(150) NULL,
                MODIFY `c_Address` VARCHAR(255) NULL,
                MODIFY `c_City` VARCHAR(100) NULL,
                MODIFY `c_PostalCode` VARCHAR(50) NULL,
                MODIFY `c_Country` VARCHAR(100) NULL,
                MODIFY `c_Phone` VARCHAR(50) NULL");
        }

        // 2. ตาราง tb_categories
        $colCat = $pdo->query("SHOW COLUMNS FROM `tb_categories` LIKE 'c_CategoryName'")->fetch(PDO::FETCH_ASSOC);
        if ($colCat && stripos($colCat['Type'], 'varchar(30)') !== false) {
            $pdo->exec("ALTER TABLE `tb_categories` 
                MODIFY `c_CategoryName` VARCHAR(255) NOT NULL,
                MODIFY `c_Description` TEXT NULL");
        }

        // 3. ตาราง tb_products
        $colProd = $pdo->query("SHOW COLUMNS FROM `tb_products` LIKE 'c_ProductName'")->fetch(PDO::FETCH_ASSOC);
        if ($colProd && stripos($colProd['Type'], 'varchar(30)') !== false) {
            $pdo->exec("ALTER TABLE `tb_products` 
                MODIFY `c_ProductName` VARCHAR(255) NOT NULL,
                MODIFY `c_Unit` VARCHAR(150) NULL");
        }

        // 4. กรณีตาราง Suppliers / Categories / Products แบบมาตรฐาน
        $colStdSup = $pdo->query("SHOW COLUMNS FROM `Suppliers` LIKE 'SupplierName'")->fetch(PDO::FETCH_ASSOC);
        if ($colStdSup && stripos($colStdSup['Type'], 'varchar(30)') !== false) {
            $pdo->exec("ALTER TABLE `Suppliers` 
                MODIFY `SupplierName` VARCHAR(255) NOT NULL,
                MODIFY `ContactName` VARCHAR(150) NULL,
                MODIFY `Address` VARCHAR(255) NULL");
        }

        $upgraded = true;
    } catch (Throwable $e) {
        error_log("Notice in upgradeNorthwindColumns: " . $e->getMessage());
    }
}


/**
 * รันไฟล์ SQL Dump อย่างปลอดภัย
 */
function importSqlDump(PDO $pdo, string $filePath): void {
    $content = file_get_contents($filePath);
    if ($content === false || trim($content) === '') return;

    // ลบบรรทัด CREATE DATABASE / USE db_northwind เพื่อให้ทำงานกับ db ปัจจุบัน (เช่น railway หรือ db_northwind) ได้อย่างไร้รอยต่อ
    $lines = explode("\n", $content);
    $cleanLines = [];
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (stripos($trimmed, 'CREATE DATABASE') === 0) continue;
        if (stripos($trimmed, 'USE `') === 0 || stripos($trimmed, 'USE ') === 0) continue;
        $cleanLines[] = $line;
    }
    $cleanSql = implode("\n", $cleanLines);

    // ทำการรัน queries
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
    try {
        $pdo->exec($cleanSql);
    } catch (Throwable $e) {
        // หากรันก้อนใหญ่ไม่ผ่าน ให้แยกแบ่งตาม delimiter ;
        runSqlChunked($pdo, $cleanSql);
    }
}

function runSqlChunked(PDO $pdo, string $sql): void {
    $statements = explode(";\n", $sql);
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '' || str_starts_with($stmt, '--') || str_starts_with($stmt, '/*')) {
            continue;
        }
        try {
            $pdo->exec($stmt);
        } catch (Throwable $e) {
            // ข้าม error ย่อย เช่น drop table if exists
        }
    }
}

/**
 * Fallback Schema ในกรณีฉุกเฉิน
 */
function createFallbackTables(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `tb_categories` (
        `i_CategoryID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `c_CategoryName` varchar(100) NOT NULL,
        `c_Description` varchar(255) NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `tb_suppliers` (
        `i_SupplierID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `c_SupplierName` varchar(100) NOT NULL,
        `c_ContactName` varchar(100) NULL,
        `c_Address` varchar(150) NULL,
        `c_City` varchar(50) NULL,
        `c_PostalCode` varchar(20) NULL,
        `c_Country` varchar(50) NULL,
        `c_Phone` varchar(50) NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `tb_products` (
        `i_ProductID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `c_ProductName` varchar(255) NOT NULL,
        `i_SupplierID` int(11) NULL,
        `i_CategoryID` int(11) NULL,
        `c_Unit` varchar(100) NOT NULL,
        `i_Price` float NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

/**
 * ตรวจสอบและคืนค่า mapping ของคอลัมน์ในตาราง
 * รองรับทั้งรูปแบบไฟล์อาจารย์ (i_ProductID, c_ProductName)
 * และรูปแบบมาตรฐาน (ProductID, ProductName)
 */
function getNorthwindSchema(PDO $pdo): array {
    static $schemaCache = null;
    if ($schemaCache !== null) {
        return $schemaCache;
    }

    $productCols = [];
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM `tb_products`");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productCols[] = $row['Field'];
        }
    } catch (Throwable $e) {
        $productCols = [];
    }

    $isPrefixed = in_array('i_ProductID', $productCols, true);

    $schemaCache = [
        'is_prefixed' => $isPrefixed,
        'products' => [
            'table'       => 'tb_products',
            'id'          => $isPrefixed ? 'i_ProductID' : 'ProductID',
            'name'        => $isPrefixed ? 'c_ProductName' : 'ProductName',
            'supplier_id' => $isPrefixed ? 'i_SupplierID' : 'SupplierID',
            'category_id' => $isPrefixed ? 'i_CategoryID' : 'CategoryID',
            'unit'        => $isPrefixed ? 'c_Unit' : 'Unit',
            'price'       => $isPrefixed ? 'i_Price' : 'Price',
        ],
        'categories' => [
            'table' => 'tb_categories',
            'id'    => $isPrefixed ? 'i_CategoryID' : 'CategoryID',
            'name'  => $isPrefixed ? 'c_CategoryName' : 'CategoryName',
            'desc'  => $isPrefixed ? 'c_Description' : 'Description',
        ],
        'suppliers' => [
            'table'       => 'tb_suppliers',
            'id'          => $isPrefixed ? 'i_SupplierID' : 'SupplierID',
            'name'        => $isPrefixed ? 'c_SupplierName' : 'SupplierName',
            'contact'     => $isPrefixed ? 'c_ContactName' : 'ContactName',
            'address'     => $isPrefixed ? 'c_Address' : 'Address',
            'city'        => $isPrefixed ? 'c_City' : 'City',
            'postal_code' => $isPrefixed ? 'c_PostalCode' : 'PostalCode',
            'country'     => $isPrefixed ? 'c_Country' : 'Country',
            'phone'       => $isPrefixed ? 'c_Phone' : 'Phone',
        ],
    ];

    return $schemaCache;
}
