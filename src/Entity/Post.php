<?php
/**
 * 게시물 엔티티
 */
class Post {
    private ?int $id;
    private string $title;
    private string $author;
    private string $content;
    private DateTime $createdAt;
    private int $viewCount;
    
    public function __construct(
        ?int $id = null,
        string $title = '',
        string $author = '',
        string $content = '',
        ?DateTime $createdAt = null,
        int $viewCount = 0
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->content = $content;
        $this->createdAt = $createdAt ?? new DateTime();
        $this->viewCount = $viewCount;
    }
    
    /**
     * 배열에서 Post 객체 생성
     */
    public static function fromArray(array $data): Post {
        return new Post(
            $data['id'] ?? null,
            $data['title'] ?? '',
            $data['author'] ?? '',
            $data['content'] ?? '',
            isset($data['created_at']) ? new DateTime($data['created_at']) : null,
            $data['view_count'] ?? 0
        );
    }
    
    /**
     * 배열로 변환
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'content' => $this->content,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'view_count' => $this->viewCount
        ];
    }
    
    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    
    public function getTitle(): string {
        return $this->title;
    }
    
    public function getAuthor(): string {
        return $this->author;
    }
    
    public function getContent(): string {
        return $this->content;
    }
    
    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }
    
    public function getViewCount(): int {
        return $this->viewCount;
    }
    
    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }
    
    public function setTitle(string $title): void {
        $this->title = $title;
    }
    
    public function setAuthor(string $author): void {
        $this->author = $author;
    }
    
    public function setContent(string $content): void {
        $this->content = $content;
    }
    
    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }
    
    public function setViewCount(int $viewCount): void {
        $this->viewCount = $viewCount;
    }
}
