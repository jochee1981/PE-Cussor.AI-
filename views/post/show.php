<?php
require_once __DIR__ . '/../../src/Helper/SecurityHelper.php';
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
        <?php if (isset($errorMessage)): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-red-800"><?php echo SecurityHelper::escapeHtml($errorMessage); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- 게시물 내용 -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-4"><?php echo SecurityHelper::escapeHtml($post->getTitle()); ?></h1>
                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                    <span>작성자: <strong><?php echo SecurityHelper::escapeHtml($post->getAuthor()); ?></strong></span>
                    <span>작성일: <?php echo SecurityHelper::escapeHtml($post->getCreatedAt()->format('Y-m-d H:i:s')); ?></span>
                    <span>조회수: <?php echo SecurityHelper::escapeHtml($post->getViewCount()); ?></span>
                </div>
            </div>
            
            <div class="prose max-w-none mb-6">
                <?php echo $post->getContent(); // CKEditor로 작성된 HTML이므로 그대로 출력 ?>
            </div>

            <div class="flex gap-3 pt-6 border-t border-gray-200">
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    목록으로
                </a>
                <a href="edit.php?id=<?php echo SecurityHelper::escapeHtml($post->getId()); ?>" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    수정
                </a>
                <a href="delete.php?id=<?php echo SecurityHelper::escapeHtml($post->getId()); ?>" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200" onclick="return confirm('정말 삭제하시겠습니까?');">
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
                        <span class="font-semibold text-gray-800"><?php echo SecurityHelper::escapeHtml($comment->getAuthor()); ?></span>
                        <span class="text-sm text-gray-500"><?php echo SecurityHelper::escapeHtml($comment->getCreatedAt()->format('Y-m-d H:i:s')); ?></span>
                    </div>
                    <p class="text-gray-700"><?php echo SecurityHelper::escapeHtml($comment->getContent()); ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- 댓글 작성 폼 -->
            <form action="comment.php" method="POST" class="border-t border-gray-200 pt-6">
                <input type="hidden" name="board_id" value="<?php echo SecurityHelper::escapeHtml($post->getId()); ?>">
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
    </div>
</body>
</html>
