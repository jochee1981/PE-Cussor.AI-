<?php
/**
 * Step 6: 데이터베이스 연결 설정
 * PDO 기반 MySQL 연결
 */

// 데이터베이스 설정
define('DB_HOST', 'localhost');
define('DB_NAME', 'cursor_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

try {
    // PDO 연결 문자열 생성
    $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
    
    // PDO 객체 생성 (DB 생성 전에는 DB명 없이 연결)
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    
    // 에러 모드 설정: 예외 발생 시 적절한 에러 메시지 표시
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 기본 fetch 모드 설정
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("데이터베이스 연결 실패: " . $e->getMessage());
}

/**
 * 데이터베이스에 연결하는 함수 (DB 생성 후 사용)
 */
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("데이터베이스 연결 실패: " . $e->getMessage());
    }
}
?>

