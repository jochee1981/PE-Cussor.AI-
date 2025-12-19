<?php
require_once __DIR__ . '/../Entity/Post.php';

/**
 * 게시물 Repository 인터페이스
 */
interface PostRepositoryInterface {
    public function findAll(): array;
    public function findById(int $id): ?Post;
    public function save(Post $post): Post;
    public function update(Post $post): bool;
    public function delete(int $id): bool;
    public function incrementViewCount(int $id): bool;
}
