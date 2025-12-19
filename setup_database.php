<?php
/**
 * Step 8: 테이블 생성 스크립트
 * board와 comments 테이블을 자동으로 생성합니다.
 */

require_once 'config.php';

try {
    // cursor_db 데이터베이스 선택
    $pdo->exec("USE cursor_db");
    
    // board 테이블 생성
    $createBoardTable = "
    CREATE TABLE IF NOT EXISTS board (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        author VARCHAR(50) NOT NULL,
        content LONGTEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        view_count INT DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($createBoardTable);
    
    // comments 테이블 생성
    $createCommentsTable = "
    CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        board_id INT NOT NULL,
        author VARCHAR(50) NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (board_id) REFERENCES board(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($createCommentsTable);
    
    echo "<!DOCTYPE html>
<html lang='ko'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>테이블 생성</title>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body class='bg-gray-50'>
    <div class='container mx-auto px-4 py-8 max-w-2xl'>
        <div class='bg-green-50 border border-green-200 rounded-lg p-6 text-center'>
            <h1 class='text-2xl font-bold text-green-800 mb-4'>✓ 테이블 생성 완료!</h1>
            <p class='text-gray-700 mb-2'>board 테이블과 comments 테이블이 성공적으로 생성되었습니다.</p>
            <p class='text-gray-700 mb-4'>이제 게시판을 사용할 수 있습니다.</p>
            <div class='space-y-2'>
                <a href='index.php' class='inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200'>
                    게시판으로 이동
                </a>
            </div>
        </div>
    </div>
</body>
</html>";
    
} catch (PDOException $e) {
    // 테이블이 이미 존재하는 경우
    if (strpos($e->getMessage(), 'already exists') !== false || strpos($e->getMessage(), 'Duplicate') !== false) {
        echo "<!DOCTYPE html>
<html lang='ko'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>테이블 생성</title>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body class='bg-gray-50'>
    <div class='container mx-auto px-4 py-8 max-w-2xl'>
        <div class='bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center'>
            <h1 class='text-2xl font-bold text-yellow-800 mb-4'>⚠ 이미 테이블이 있습니다</h1>
            <p class='text-gray-700 mb-4'>테이블이 이미 존재합니다. 게시판을 사용할 수 있습니다.</p>
            <a href='index.php' class='inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200'>
                게시판으로 이동
            </a>
        </div>
    </div>
</body>
</html>";
    } else {
        echo "<!DOCTYPE html>
<html lang='ko'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>테이블 생성 오류</title>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body class='bg-gray-50'>
    <div class='container mx-auto px-4 py-8 max-w-2xl'>
        <div class='bg-red-50 border border-red-200 rounded-lg p-6 text-center'>
            <h1 class='text-2xl font-bold text-red-800 mb-4'>✗ 테이블 생성 실패</h1>
            <p class='text-gray-700 mb-4'>오류 메시지: " . htmlspecialchars($e->getMessage()) . "</p>
            <a href='setup_database.php' class='inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200'>
                다시 시도
            </a>
        </div>
    </div>
</body>
</html>";
    }
}
?>

