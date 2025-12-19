<?php
/**
 * 댓글 엔티티
 */
class Comment {
    private ?int $id;
    private int $boardId;
    private string $author;
    private string $content;
    private DateTime $createdAt;
    
    public function __construct(
        ?int $id = null,
        int $boardId = 0,
        string $author = '',
        string $content = '',
        ?DateTime $createdAt = null
    ) {
        $this->id = $id;
        $this->boardId = $boardId;
        $this->author = $author;
        $this->content = $content;
        $this->createdAt = $createdAt ?? new DateTime();
    }
    
    /**
     * 배열에서 Comment 객체 생성
     */
    public static function fromArray(array $data): Comment {
        return new Comment(
            $data['id'] ?? null,
            $data['board_id'] ?? 0,
            $data['author'] ?? '',
            $data['content'] ?? '',
            isset($data['created_at']) ? new DateTime($data['created_at']) : null
        );
    }
    
    /**
     * 배열로 변환
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'board_id' => $this->boardId,
            'author' => $this->author,
            'content' => $this->content,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s')
        ];
    }
    
    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    
    public function getBoardId(): int {
        return $this->boardId;
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
    
    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }
    
    public function setBoardId(int $boardId): void {
        $this->boardId = $boardId;
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
}
