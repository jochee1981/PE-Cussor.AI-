<?php
require_once __DIR__ . '/../../src/Helper/SecurityHelper.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>글 수정</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">글 수정</h1>
            
            <?php if (isset($errorMessage)): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-red-800"><?php echo SecurityHelper::escapeHtml($errorMessage); ?></p>
            </div>
            <?php endif; ?>
            
            <form action="edit.php" method="POST" class="space-y-6">
                <input type="hidden" name="id" value="<?php echo SecurityHelper::escapeHtml($post->getId()); ?>">
                
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">제목</label>
                    <input type="text" id="title" name="title" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           value="<?php echo SecurityHelper::escapeHtml($post->getTitle()); ?>"
                           placeholder="제목을 입력하세요">
                </div>
                
                <div>
                    <label for="author" class="block text-sm font-medium text-gray-700 mb-2">작성자</label>
                    <input type="text" id="author" name="author" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           value="<?php echo SecurityHelper::escapeHtml($post->getAuthor()); ?>"
                           placeholder="작성자명을 입력하세요">
                </div>
                
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">내용</label>
                    <textarea id="content" name="content" rows="10" required 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="내용을 입력하세요"><?php echo SecurityHelper::escapeHtml($post->getContent()); ?></textarea>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                        수정하기
                    </button>
                    <a href="view.php?id=<?php echo SecurityHelper::escapeHtml($post->getId()); ?>" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 text-center">
                        취소
                    </a>
                </div>
            </form>
        </div>
    </div>
    
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
