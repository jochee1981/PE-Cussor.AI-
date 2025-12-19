<?php
/**
 * write.php 디버깅 테스트 파일
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>디버깅 테스트</h1>";

// 1. Container 로드 테스트
echo "<h2>1. Container 로드 테스트</h2>";
try {
    require_once __DIR__ . '/src/Container.php';
    echo "✅ Container.php 로드 성공<br>";
} catch (Throwable $e) {
    echo "❌ Container.php 로드 실패: " . $e->getMessage() . "<br>";
    echo "파일: " . $e->getFile() . " 라인: " . $e->getLine() . "<br>";
    exit;
}

// 2. PostController 가져오기 테스트
echo "<h2>2. PostController 가져오기 테스트</h2>";
try {
    $controller = Container::getPostController();
    echo "✅ PostController 가져오기 성공<br>";
    echo "타입: " . get_class($controller) . "<br>";
} catch (Throwable $e) {
    echo "❌ PostController 가져오기 실패: " . $e->getMessage() . "<br>";
    echo "파일: " . $e->getFile() . " 라인: " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    exit;
}

// 3. POST 데이터 테스트
echo "<h2>3. POST 데이터 테스트</h2>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST 데이터:<br>";
    echo "title: " . ($_POST['title'] ?? '없음') . "<br>";
    echo "author: " . ($_POST['author'] ?? '없음') . "<br>";
    echo "content: " . (isset($_POST['content']) ? substr($_POST['content'], 0, 50) . '...' : '없음') . "<br>";
    
    try {
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'author' => trim($_POST['author'] ?? ''),
            'content' => trim($_POST['content'] ?? '')
        ];
        
        echo "<h2>4. 게시물 등록 테스트</h2>";
        $post = $controller->store($data);
        echo "✅ 게시물 등록 성공!<br>";
    } catch (Throwable $e) {
        echo "❌ 게시물 등록 실패: " . $e->getMessage() . "<br>";
        echo "파일: " . $e->getFile() . " 라인: " . $e->getLine() . "<br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "GET 요청입니다. POST 요청을 보내주세요.<br>";
    echo "<form method='POST'>";
    echo "<input type='text' name='title' placeholder='제목' required><br>";
    echo "<input type='text' name='author' placeholder='작성자' required><br>";
    echo "<textarea name='content' placeholder='내용' required></textarea><br>";
    echo "<button type='submit'>테스트 제출</button>";
    echo "</form>";
}
