<?php
require_once __DIR__ . '/../Service/PostService.php';
require_once __DIR__ . '/../Service/CommentService.php';
require_once __DIR__ . '/../Helper/SecurityHelper.php';
require_once __DIR__ . '/../Helper/ResponseHelper.php';

/**
 * 게시물 컨트롤러
 */
class PostController {
    private PostService $postService;
    private CommentService $commentService;
    
    public function __construct(PostService $postService, CommentService $commentService) {
        $this->postService = $postService;
        $this->commentService = $commentService;
    }
    
    public function index(): void {
        try {
            $posts = $this->postService->getAllPosts();
            $errorMessage = $_GET['error'] ?? null;
            $successMessage = $_GET['success'] ?? null;
            
            require __DIR__ . '/../../views/post/index.php';
        } catch (Exception $e) {
            ResponseHelper::errorResponse($e->getMessage());
        }
    }
    
    public function show(int $id): void {
        try {
            $post = $this->postService->getPost($id);
            if (!$post) {
                ResponseHelper::errorResponse('게시물을 찾을 수 없습니다.');
                return;
            }
            
            // 조회수 증가
            $this->postService->incrementViewCount($id);
            $post = $this->postService->getPost($id); // 업데이트된 조회수 가져오기
            
            $comments = $this->commentService->getCommentsByBoardId($id);
            $errorMessage = $_GET['error'] ?? null;
            
            require __DIR__ . '/../../views/post/show.php';
        } catch (Exception $e) {
            ResponseHelper::errorResponse($e->getMessage());
        }
    }
    
    public function create(): void {
        $errorMessage = $_GET['error'] ?? null;
        require __DIR__ . '/../../views/post/create.php';
    }
    
    public function store(array $data): void {
        try {
            $post = $this->postService->createPost($data);
            ResponseHelper::successResponse('게시물이 등록되었습니다.', 'index.php');
        } catch (Exception $e) {
            ResponseHelper::errorResponse($e->getMessage(), 'write.php');
        }
    }
    
    public function edit(int $id): void {
        try {
            $post = $this->postService->getPost($id);
            if (!$post) {
                ResponseHelper::errorResponse('게시물을 찾을 수 없습니다.');
                return;
            }
            
            $errorMessage = $_GET['error'] ?? null;
            require __DIR__ . '/../../views/post/edit.php';
        } catch (Exception $e) {
            ResponseHelper::errorResponse($e->getMessage());
        }
    }
    
    public function update(int $id, array $data): void {
        try {
            $post = $this->postService->updatePost($id, $data);
            ResponseHelper::successResponse('게시물이 수정되었습니다.', 'view.php?id=' . $id);
        } catch (Exception $e) {
            ResponseHelper::errorResponse($e->getMessage(), 'edit.php?id=' . $id);
        }
    }
    
    public function delete(int $id): void {
        try {
            $this->postService->deletePost($id);
            ResponseHelper::successResponse('게시물이 삭제되었습니다.');
        } catch (Exception $e) {
            ResponseHelper::errorResponse($e->getMessage());
        }
    }
}
