<?php
require_once __DIR__ . '/../../src/Helper/SecurityHelper.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시판 목록</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">게시판</h1>
            <a href="write.php" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                글쓰기
            </a>
        </div>

        <?php if (isset($errorMessage)): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-red-800"><?php echo SecurityHelper::escapeHtml($errorMessage); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (isset($successMessage)): ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-green-800"><?php echo SecurityHelper::escapeHtml($successMessage); ?></p>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">번호</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">제목</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">작성자</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">작성일</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">조회수</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($posts)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            등록된 게시물이 없습니다.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo SecurityHelper::escapeHtml($post->getId()); ?></td>
                        <td class="px-6 py-4 text-sm">
                            <a href="view.php?id=<?php echo SecurityHelper::escapeHtml($post->getId()); ?>" class="text-blue-600 hover:text-blue-800 hover:underline">
                                <?php echo SecurityHelper::escapeHtml($post->getTitle()); ?>
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo SecurityHelper::escapeHtml($post->getAuthor()); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo SecurityHelper::escapeHtml($post->getCreatedAt()->format('Y-m-d H:i:s')); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo SecurityHelper::escapeHtml($post->getViewCount()); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
