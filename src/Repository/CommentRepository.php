<?php
require_once __DIR__ . '/CommentRepositoryInterface.php';
require_once __DIR__ . '/../Entity/Comment.php';

/**
 * 댓글 Repository 구현체
 */
class CommentRepository implements CommentRepositoryInterface {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function findByBoardId(int $boardId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE board_id = ? ORDER BY created_at DESC");
        $stmt->execute([$boardId]);
        $rows = $stmt->fetchAll();
        
        return array_map(function($row) {
            return Comment::fromArray($row);
        }, $rows);
    }
    
    public function save(Comment $comment): Comment {
        $stmt = $this->pdo->prepare(
            "INSERT INTO comments (board_id, author, content, created_at) 
             VALUES (?, ?, ?, NOW())"
        );
        $stmt->execute([
            $comment->getBoardId(),
            $comment->getAuthor(),
            $comment->getContent()
        ]);
        
        $comment->setId((int)$this->pdo->lastInsertId());
        return $comment;
    }
    
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function deleteByBoardId(int $boardId): bool {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE board_id = ?");
        return $stmt->execute([$boardId]);
    }
}
