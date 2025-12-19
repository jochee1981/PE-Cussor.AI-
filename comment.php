<?php
/**
 * Step 12: 댓글 기능
 * 댓글 등록 처리
 */

require_once 'config.php';

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        
        // POST 데이터 받기 및 XSS 방지
        $board_id = isset($_POST['board_id']) ? (int)$_POST['board_id'] : 0;
        $author = isset($_POST['author']) ? htmlspecialchars(trim($_POST['author']), ENT_QUOTES, 'UTF-8') : '';
        $content = isset($_POST['content']) ? htmlspecialchars(trim($_POST['content']), ENT_QUOTES, 'UTF-8') : '';
        
        // 유효성 검사
        if ($board_id === 0 || empty($author) || empty($content)) {
            throw new Exception('모든 필드를 입력해주세요.');
        }
        
        // 길이 제한 검증
        if (strlen($author) > 50) {
            throw new Exception('작성자 이름은 50자 이하여야 합니다.');
        }
        
        // 게시물 존재 확인
        $stmt = $pdo->prepare("SELECT id FROM board WHERE id = ?");
        $stmt->execute([$board_id]);
        if (!$stmt->fetch()) {
            throw new Exception('존재하지 않는 게시물입니다.');
        }
        
        // PDO Prepared Statement로 SQL Injection 방지
        $stmt = $pdo->prepare("INSERT INTO comments (board_id, author, content) VALUES (?, ?, ?)");
        $stmt->execute([$board_id, $author, $content]);
        
        // 성공 시 view.php로 리다이렉트
        header("Location: view.php?id=" . $board_id);
        exit;
        
    } catch (Exception $e) {
        // 에러 발생 시 이전 페이지로 리다이렉트 또는 에러 메시지 표시
        if ($board_id > 0) {
            header("Location: view.php?id=" . $board_id . "&error=" . urlencode($e->getMessage()));
        } else {
            header("Location: index.php?error=" . urlencode($e->getMessage()));
        }
        exit;
    }
} else {
    // GET 요청 시 index.php로 리다이렉트
    header("Location: index.php");
    exit;
}
?>

