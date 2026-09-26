<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/db.php';

$startTime = microtime(true);

try {
    $pdo = db();
    $schema = getNorthwindSchema($pdo);

    $pTable = $schema['products']['table'];
    $cTable = $schema['categories']['table'];
    $sTable = $schema['suppliers']['table'];

    $prodCount = (int)$pdo->query("SELECT COUNT(*) FROM `{$pTable}`")->fetchColumn();
    $catCount  = (int)$pdo->query("SELECT COUNT(*) FROM `{$cTable}`")->fetchColumn();
    $supCount  = (int)$pdo->query("SELECT COUNT(*) FROM `{$sTable}`")->fetchColumn();

    $dbCfg = getDbConfig();
    $dbName = $pdo->query("SELECT DATABASE()")->fetchColumn() ?: $dbCfg['db'];

    $latencyMs = round((microtime(true) - $startTime) * 1000, 2);

    http_response_code(200);
    echo json_encode([
        'success'   => true,
        'status'    => 'healthy',
        'database'  => 'connected',
        'database_name' => $dbName,
        'db_host'   => $dbCfg['host'],
        'db_port'   => $dbCfg['port'],
        'counts'    => [
            'products'   => $prodCount,
            'categories' => $catCount,
            'suppliers'  => $supCount,
        ],
        'schema_mode' => $schema['is_prefixed'] ? 'prefixed (i_ProductID / dbNorthwind.sql)' : 'standard (ProductID)',
        'latency_ms'  => $latencyMs,
        'php_version' => PHP_VERSION,
        'server_time' => date('Y-m-d H:i:s T'),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    http_response_code(503);
    $rawUrl = envVal('DATABASE_URL') ?: envVal('MYSQL_URL') ?: envVal('MYSQL_PRIVATE_URL') ?: '';
    $maskedUrl = $rawUrl !== '' ? preg_replace('/:[^:@]+@/', ':****@', $rawUrl) : '(not set / empty)';
    $rawHost = envVal('MYSQLHOST') ?: envVal('DB_HOST') ?: '(not set)';

    echo json_encode([
        'success'   => false,
        'status'    => 'unhealthy',
        'database'  => 'disconnected',
        'message'   => $e->getMessage(),
        'diagnostics' => [
            'detected_DATABASE_URL' => $maskedUrl,
            'detected_MYSQLHOST'    => $rawHost,
            'tip' => 'หาก detected_DATABASE_URL ขึ้นว่า (not set) หรือขึ้นต้นด้วย ${{ แสดงว่ายังไม่ได้ใส่ค่า URL จริง ให้ไปที่กล่อง MySQL -> Variables -> ก๊อปปี้ MYSQL_PRIVATE_URL มาวางแทนครับ',
        ],
        'php_version' => PHP_VERSION,
        'server_time' => date('Y-m-d H:i:s T'),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
