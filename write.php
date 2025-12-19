<?php
/**
 * 글 등록 기능 (리팩토링 버전)
 * POST 데이터를 PostController를 통해 처리
 */

require_once __DIR__ . '/src/Container.php';
require_once __DIR__ . '/src/Helper/SecurityHelper.php';

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // POST 데이터 확인 (디버깅)
        error_log('POST data received: ' . print_r($_POST, true));
        
        $controller = Container::getPostController();
        
        // POST 데이터 받기 (trim만 적용, htmlspecialchars는 출력 시 적용)
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'author' => trim($_POST['author'] ?? ''),
            'content' => trim($_POST['content'] ?? '') // CKEditor HTML 콘텐츠
        ];
        
        error_log('Processed data: title=' . strlen($data['title']) . ' chars, author=' . strlen($data['author']) . ' chars, content=' . strlen($data['content']) . ' chars');
        
        // Controller를 통해 게시물 등록
        $controller->store($data);
        exit; // store() 메서드에서 리다이렉트하므로 여기서는 실행되지 않음
        
    } catch (Throwable $e) {
        // 모든 예외와 에러를 캐치
        $error_message = '오류가 발생했습니다: ' . $e->getMessage();
        
        // 디버깅을 위해 에러 로그 출력
        error_log('Post creation error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        error_log('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
        
        // 개발 환경에서 상세 에러 표시
        if (ini_get('display_errors')) {
            $error_message .= ' (파일: ' . basename($e->getFile()) . ', 라인: ' . $e->getLine() . ')';
        }
    }
}
// GET 요청은 아래 HTML 폼을 표시
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
                    <textarea id="content" name="content" rows="10" 
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
        let editor;
        
        // CKEditor 초기화
        ClassicEditor
            .create(document.querySelector('#content'), {
                language: 'ko'
            })
            .then(createdEditor => {
                editor = createdEditor;
                console.log('✅ CKEditor initialized successfully');
            })
            .catch(error => {
                console.error('❌ CKEditor initialization error:', error);
            });
        
        // 폼 제출 처리 - CKEditor 동기화 및 유효성 검사
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                console.log('📝 Form submit event triggered');
                
                // CKEditor가 초기화된 경우 내용을 textarea에 동기화
                if (editor) {
                    try {
                        editor.updateSourceElement();
                        console.log('✅ CKEditor content synced to textarea');
                    } catch (error) {
                        console.error('❌ Error syncing CKEditor content:', error);
                    }
                }
                
                // 유효성 검사
                const title = document.querySelector('#title').value.trim();
                const author = document.querySelector('#author').value.trim();
                const contentElement = document.querySelector('#content');
                const content = contentElement ? contentElement.value.trim() : '';
                
                console.log('Form validation:', {
                    title: title.length + ' chars',
                    author: author.length + ' chars',
                    content: content.length + ' chars'
                });
                
                // 유효성 검사 실패 시 제출 중단
                if (!title || !author || !content) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('모든 필드를 입력해주세요.\n\n제목: ' + (title ? '✓' : '✗') + '\n작성자: ' + (author ? '✓' : '✗') + '\n내용: ' + (content ? '✓' : '✗'));
                    console.log('❌ Validation failed - preventing submit');
                    return false;
                }
                
                // 유효성 검사 통과 - 폼 제출 허용
                console.log('✅ Form validation passed');
                console.log('🚀 Form submission allowed');
            });
        }
        
        // 취소 버튼 클릭 이벤트
        const cancelLink = document.querySelector('a[href="index.php"]');
        if (cancelLink) {
            cancelLink.addEventListener('click', function(e) {
                const title = document.querySelector('#title').value.trim();
                const author = document.querySelector('#author').value.trim();
                const contentElement = document.querySelector('#content');
                const content = contentElement ? contentElement.value.trim() : '';
                
                // 입력된 내용이 있으면 확인
                if (title || author || content) {
                    if (!confirm('작성 중인 내용이 있습니다. 정말 취소하시겠습니까?')) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
        }
    </script>
</body>
</html>

