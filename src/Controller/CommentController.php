<?php
require_once __DIR__ . '/../Service/CommentService.php';
require_once __DIR__ . '/../Helper/ResponseHelper.php';

/**
 * 댓글 컨트롤러
 */
class CommentController {
    private CommentService $commentService;
    
    public function __construct(CommentService $commentService) {
        $this->commentService = $commentService;
    }
    
    public function store(array $data): void {
        try {
            $comment = $this->commentService->createComment($data);
            ResponseHelper::successResponse('댓글이 등록되었습니다.', 'view.php?id=' . ($data['board_id'] ?? 0));
        } catch (Exception $e) {
            $boardId = $data['board_id'] ?? 0;
            if ($boardId > 0) {
                ResponseHelper::errorResponse($e->getMessage(), 'view.php?id=' . $boardId);
            } else {
                ResponseHelper::errorResponse($e->getMessage());
            }
        }
    }
}
