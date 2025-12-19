<?php
require_once __DIR__ . '/PostRepositoryInterface.php';
require_once __DIR__ . '/../Entity/Post.php';
require_once __DIR__ . '/../../config/database.php';

/**
 * 게시물 Repository 구현체
 */
class PostRepository implements PostRepositoryInterface {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM board ORDER BY id DESC");
        $rows = $stmt->fetchAll();
        
        return array_map(function($row) {
            return Post::fromArray($row);
        }, $rows);
    }
    
    public function findById(int $id): ?Post {
        $stmt = $this->pdo->prepare("SELECT * FROM board WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        return Post::fromArray($row);
    }
    
    public function save(Post $post): Post {
        $stmt = $this->pdo->prepare(
            "INSERT INTO board (title, author, content, created_at, view_count) 
             VALUES (?, ?, ?, NOW(), ?)"
        );
        $stmt->execute([
            $post->getTitle(),
            $post->getAuthor(),
            $post->getContent(),
            $post->getViewCount()
        ]);
        
        $post->setId((int)$this->pdo->lastInsertId());
        return $post;
    }
    
    public function update(Post $post): bool {
        if ($post->getId() === null) {
            return false;
        }
        
        $stmt = $this->pdo->prepare(
            "UPDATE board SET title = ?, author = ?, content = ? WHERE id = ?"
        );
        
        return $stmt->execute([
            $post->getTitle(),
            $post->getAuthor(),
            $post->getContent(),
            $post->getId()
        ]);
    }
    
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM board WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function incrementViewCount(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE board SET view_count = view_count + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
