<?php
require_once __DIR__ . '/../Entity/Comment.php';

/**
 * 댓글 Repository 인터페이스
 */
interface CommentRepositoryInterface {
    public function findByBoardId(int $boardId): array;
    public function save(Comment $comment): Comment;
    public function delete(int $id): bool;
    public function deleteByBoardId(int $boardId): bool;
}
