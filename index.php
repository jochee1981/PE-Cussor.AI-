<?php
/**
 * Step 10: 목록 조회
 * DB에서 게시물 목록을 동적으로 불러오기
 */

require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Step 2: 최신글 우선 (내림차순) 정렬
    $stmt = $pdo->query("SELECT * FROM board ORDER BY id DESC");
    $posts = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $posts = [];
    $error_message = "게시물을 불러오는 중 오류가 발생했습니다: " . $e->getMessage();
}
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

        <?php if (isset($error_message)): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-red-800"><?php echo htmlspecialchars($error_message); ?></p>
            <p class="text-sm text-gray-600 mt-2">데이터베이스가 설정되지 않았을 수 있습니다. <a href="create_database.php" class="text-blue-600 underline">데이터베이스 생성하기</a></p>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-red-800"><?php echo htmlspecialchars($_GET['error']); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-green-800"><?php echo htmlspecialchars($_GET['success']); ?></p>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($post['id']); ?></td>
                        <td class="px-6 py-4 text-sm">
                            <a href="view.php?id=<?php echo htmlspecialchars($post['id']); ?>" class="text-blue-600 hover:text-blue-800 hover:underline">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($post['author']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($post['created_at']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($post['view_count']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

