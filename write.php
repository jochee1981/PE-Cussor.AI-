<?php
/**
 * Step 9: 글 등록 기능
 * POST 데이터를 board 테이블에 INSERT
 */

require_once 'config.php';

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        
        // POST 데이터 받기 및 XSS 방지
        $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8') : '';
        $author = isset($_POST['author']) ? htmlspecialchars(trim($_POST['author']), ENT_QUOTES, 'UTF-8') : '';
        $content = isset($_POST['content']) ? $_POST['content'] : ''; // CKEditor는 HTML이므로 htmlspecialchars는 나중에 출력 시 적용
        
        // 유효성 검사
        if (empty($title) || empty($author) || empty($content)) {
            throw new Exception('모든 필드를 입력해주세요.');
        }
        
        // PDO Prepared Statement로 SQL Injection 방지
        $stmt = $pdo->prepare("INSERT INTO board (title, author, content) VALUES (?, ?, ?)");
        $stmt->execute([$title, $author, $content]);
        
        // 성공 시 index.php로 리다이렉트
        header("Location: index.php");
        exit;
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>글쓰기</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Step 5: CKEditor 5 Classic CDN -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">글쓰기</h1>
            
            <?php if (isset($error_message)): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-red-800"><?php echo htmlspecialchars($error_message); ?></p>
            </div>
            <?php endif; ?>
            
            <form action="write.php" method="POST" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">제목</label>
                    <input type="text" id="title" name="title" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="제목을 입력하세요">
                </div>
                
                <div>
                    <label for="author" class="block text-sm font-medium text-gray-700 mb-2">작성자</label>
                    <input type="text" id="author" name="author" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="작성자명을 입력하세요">
                </div>
                
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">내용</label>
                    <textarea id="content" name="content" rows="10" required 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="내용을 입력하세요"></textarea>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                        등록하기
                    </button>
                    <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 text-center">
                        취소
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Step 5: CKEditor 초기화 -->
    <script>
        ClassicEditor
            .create(document.querySelector('#content'), {
                language: 'ko'
            })
            .catch(error => {
                console.error(error);
            });
    </script>
</body>
</html>

