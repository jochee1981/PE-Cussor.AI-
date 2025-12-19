<?php
require_once __DIR__ . '/../Repository/CommentRepositoryInterface.php';
require_once __DIR__ . '/../Repository/PostRepositoryInterface.php';
require_once __DIR__ . '/../Validator/CommentValidator.php';
require_once __DIR__ . '/../Entity/Comment.php';

/**
 * 댓글 서비스 클래스
 */
class CommentService {
    private CommentRepositoryInterface $commentRepository;
    private PostRepositoryInterface $postRepository;
    private CommentValidator $validator;
    
    public function __construct(
        CommentRepositoryInterface $commentRepository,
        PostRepositoryInterface $postRepository,
        CommentValidator $validator
    ) {
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
        $this->validator = $validator;
    }
    
    public function getCommentsByBoardId(int $boardId): array {
        return $this->commentRepository->findByBoardId($boardId);
    }
    
    public function createComment(array $data): Comment {
        // 게시물 존재 확인
        $post = $this->postRepository->findById($data['board_id'] ?? 0);
        if (!$post) {
            throw new Exception('존재하지 않는 게시물입니다.');
        }
        
        $comment = new Comment(
            null,
            $data['board_id'] ?? 0,
            $data['author'] ?? '',
            $data['content'] ?? ''
        );
        
        $validationResult = $this->validator->validate($comment);
        if (!$validationResult->isValid()) {
            throw new Exception($validationResult->getFirstError());
        }
        
        return $this->commentRepository->save($comment);
    }
}
