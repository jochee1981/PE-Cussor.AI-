<?php
/**
 * Step 11: 상세보기 + 조회수
 * 게시물 조회 시 view_count 증가 및 DB에서 데이터 가져오기
 * Step 12: 댓글 기능
 * comments 테이블에서 댓글 조회
 */

require_once 'config.php';

// GET 파라미터로 id 받기
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    $post = null;
    $comments = [];
} else {
    try {
        $pdo = getDBConnection();
        
        // Step 11: 조회수 증가
        $stmt = $pdo->prepare("UPDATE board SET view_count = view_count + 1 WHERE id = ?");
        $stmt->execute([$id]);
        
        // 게시물 조회
        $stmt = $pdo->prepare("SELECT * FROM board WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch();
        
        // Step 12: 댓글 조회 (최신순)
        if ($post) {
            $stmt = $pdo->prepare("SELECT * FROM comments WHERE board_id = ? ORDER BY created_at DESC");
            $stmt->execute([$id]);
            $comments = $stmt->fetchAll();
        } else {
            $comments = [];
        }
        
    } catch (PDOException $e) {
        $post = null;
        $comments = [];
        $error_message = "게시물을 불러오는 중 오류가 발생했습니다: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시물 상세보기</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <?php if (!$post): ?>
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <p class="text-gray-600 text-lg">게시물이 없습니다.</p>
                <?php if (isset($error_message)): ?>
                <p class="text-red-600 text-sm mt-2"><?php echo htmlspecialchars($error_message); ?></p>
                <?php endif; ?>
                <a href="index.php" class="mt-4 inline-block text-blue-600 hover:text-blue-800">목록으로 돌아가기</a>
            </div>
        <?php else: ?>
        <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-red-800"><?php echo htmlspecialchars($_GET['error']); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- 게시물 내용 -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($post['title']); ?></h1>
                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                    <span>작성자: <strong><?php echo htmlspecialchars($post['author']); ?></strong></span>
                    <span>작성일: <?php echo htmlspecialchars($post['created_at']); ?></span>
                    <span>조회수: <?php echo htmlspecialchars($post['view_count']); ?></span>
                </div>
            </div>
            
            <div class="prose max-w-none mb-6">
                <?php echo $post['content']; // CKEditor로 작성된 HTML이므로 그대로 출력 ?>
            </div>

            <div class="flex gap-3 pt-6 border-t border-gray-200">
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    목록으로
                </a>
                <a href="edit.php?id=<?php echo htmlspecialchars($post['id']); ?>" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    수정
                </a>
                <a href="delete.php?id=<?php echo htmlspecialchars($post['id']); ?>" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200" onclick="return confirm('정말 삭제하시겠습니까?');">
                    삭제
                </a>
            </div>
        </div>

        <!-- 댓글 영역 -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">댓글 (<?php echo count($comments); ?>)</h2>
            
            <!-- 댓글 목록 -->
            <div class="space-y-4 mb-8">
                <?php foreach ($comments as $comment): ?>
                <div class="border-b border-gray-200 pb-4">
                    <div class="flex justify-between items-start mb-2">
                        <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($comment['author']); ?></span>
                        <span class="text-sm text-gray-500"><?php echo htmlspecialchars($comment['created_at']); ?></span>
                    </div>
                    <p class="text-gray-700"><?php echo htmlspecialchars($comment['content']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- 댓글 작성 폼 -->
            <form action="comment.php" method="POST" class="border-t border-gray-200 pt-6">
                <input type="hidden" name="board_id" value="<?php echo htmlspecialchars($post['id']); ?>">
                <div class="space-y-4">
                    <div>
                        <label for="author" class="block text-sm font-medium text-gray-700 mb-2">작성자</label>
                        <input type="text" id="author" name="author" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">댓글 내용</label>
                        <textarea id="content" name="content" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        댓글 작성
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

