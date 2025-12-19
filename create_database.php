<?php
/**
 * Step 7: 데이터베이스 생성 스크립트
 * cursor_db 데이터베이스를 자동으로 생성합니다.
 */

require_once 'config.php';

try {
    // 데이터베이스 생성 쿼리
    $sql = "CREATE DATABASE IF NOT EXISTS cursor_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    
    echo "<!DOCTYPE html>
<html lang='ko'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>데이터베이스 생성</title>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body class='bg-gray-50'>
    <div class='container mx-auto px-4 py-8 max-w-2xl'>
        <div class='bg-green-50 border border-green-200 rounded-lg p-6 text-center'>
            <h1 class='text-2xl font-bold text-green-800 mb-4'>✓ 데이터베이스 생성 완료!</h1>
            <p class='text-gray-700 mb-4'>cursor_db 데이터베이스가 성공적으로 생성되었습니다.</p>
            <a href='setup_database.php' class='inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200'>
                다음 단계: 테이블 생성하기
            </a>
        </div>
    </div>
</body>
</html>";
    
} catch (PDOException $e) {
    echo "<!DOCTYPE html>
<html lang='ko'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>데이터베이스 생성 오류</title>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body class='bg-gray-50'>
    <div class='container mx-auto px-4 py-8 max-w-2xl'>
        <div class='bg-red-50 border border-red-200 rounded-lg p-6 text-center'>
            <h1 class='text-2xl font-bold text-red-800 mb-4'>✗ 데이터베이스 생성 실패</h1>
            <p class='text-gray-700 mb-4'>오류 메시지: " . htmlspecialchars($e->getMessage()) . "</p>
            <p class='text-sm text-gray-600 mb-4'>XAMPP의 MySQL이 실행 중인지 확인해주세요.</p>
            <a href='create_database.php' class='inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200'>
                다시 시도
            </a>
        </div>
    </div>
</body>
</html>";
}
?>

