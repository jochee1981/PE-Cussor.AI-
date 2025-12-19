<?php
require_once __DIR__ . '/../Repository/PostRepositoryInterface.php';
require_once __DIR__ . '/../Repository/CommentRepositoryInterface.php';
require_once __DIR__ . '/../Validator/PostValidator.php';
require_once __DIR__ . '/../Entity/Post.php';

/**
 * 게시물 서비스 클래스
 */
class PostService {
    private PostRepositoryInterface $postRepository;
    private CommentRepositoryInterface $commentRepository;
    private PostValidator $validator;
    
    public function __construct(
        PostRepositoryInterface $postRepository,
        CommentRepositoryInterface $commentRepository,
        PostValidator $validator
    ) {
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->validator = $validator;
    }
    
    public function getAllPosts(): array {
        return $this->postRepository->findAll();
    }
    
    public function getPost(int $id): ?Post {
        return $this->postRepository->findById($id);
    }
    
    public function createPost(array $data): Post {
        $post = new Post(
            null,
            $data['title'] ?? '',
            $data['author'] ?? '',
            $data['content'] ?? ''
        );
        
        $validationResult = $this->validator->validate($post);
        if (!$validationResult->isValid()) {
            throw new Exception($validationResult->getFirstError());
        }
        
        return $this->postRepository->save($post);
    }
    
    public function updatePost(int $id, array $data): Post {
        $post = $this->postRepository->findById($id);
        if (!$post) {
            throw new Exception('게시물을 찾을 수 없습니다.');
        }
        
        $post->setTitle($data['title'] ?? $post->getTitle());
        $post->setAuthor($data['author'] ?? $post->getAuthor());
        $post->setContent($data['content'] ?? $post->getContent());
        
        $validationResult = $this->validator->validate($post);
        if (!$validationResult->isValid()) {
            throw new Exception($validationResult->getFirstError());
        }
        
        $this->postRepository->update($post);
        return $post;
    }
    
    public function deletePost(int $id): bool {
        $post = $this->postRepository->findById($id);
        if (!$post) {
            throw new Exception('게시물을 찾을 수 없습니다.');
        }
        
        // 관련 댓글 삭제
        $this->commentRepository->deleteByBoardId($id);
        
        // 게시물 삭제
        return $this->postRepository->delete($id);
    }
    
    public function incrementViewCount(int $id): bool {
        return $this->postRepository->incrementViewCount($id);
    }
}
