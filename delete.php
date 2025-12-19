<?php
/**
 * Bonus: 글 삭제 기능
 * DELETE 쿼리로 해당 게시물 삭제 (관련 댓글도 자동 삭제 - CASCADE)
 */

require_once 'config.php';

// GET 파라미터로 id 받기
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header("Location: index.php");
    exit;
}

try {
    $pdo = getDBConnection();
    
    // 게시물 존재 확인
    $stmt = $pdo->prepare("SELECT id FROM board WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        header("Location: index.php?error=" . urlencode("존재하지 않는 게시물입니다."));
        exit;
    }
    
    // PDO Prepared Statement로 SQL Injection 방지
    // FOREIGN KEY CASCADE로 인해 관련 댓글도 자동 삭제됨
    $stmt = $pdo->prepare("DELETE FROM board WHERE id = ?");
    $stmt->execute([$id]);
    
    // 성공 시 index.php로 리다이렉트
    header("Location: index.php?success=" . urlencode("게시물이 삭제되었습니다."));
    exit;
    
} catch (PDOException $e) {
    header("Location: index.php?error=" . urlencode("삭제 중 오류가 발생했습니다: " . $e->getMessage()));
    exit;
}
?>

