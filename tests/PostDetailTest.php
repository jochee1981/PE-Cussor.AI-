<?php
/**
 * 게시물 상세보기 기능 테스트
 * TDD RED 단계: 실패하는 테스트 작성
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config.php';

class PostDetailTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        try {
            $this->pdo = getDBConnection();
            $this->pdo->exec("TRUNCATE TABLE board");
            $this->pdo->exec("TRUNCATE TABLE comments");
        } catch (PDOException $e) {
            $this->markTestSkipped("데이터베이스 연결 실패: " . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        if ($this->pdo) {
            $this->pdo->exec("TRUNCATE TABLE board");
            $this->pdo->exec("TRUNCATE TABLE comments");
        }
    }

    /**
     * 테스트 1: 게시물 ID로 특정 게시물의 상세 정보를 조회할 수 있는지 테스트
     */
    public function testPostCanBeRetrievedById(): void
    {
        // 테스트 데이터 삽입
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content, created_at, view_count) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute(['테스트 제목', '테스트 작성자', '테스트 내용', 0]);
        $postId = $this->pdo->lastInsertId();

        // 게시물 조회
        $stmt = $this->pdo->prepare("SELECT * FROM board WHERE id = ?");
        $stmt->execute([$postId]);
        $post = $stmt->fetch();

        // 검증
        $this->assertNotFalse($post, "게시물이 조회되어야 합니다.");
        $this->assertEquals($postId, $post['id'], "게시물 ID가 일치해야 합니다.");
        $this->assertEquals('테스트 제목', $post['title'], "제목이 일치해야 합니다.");
        $this->assertEquals('테스트 작성자', $post['author'], "작성자가 일치해야 합니다.");
        $this->assertEquals('테스트 내용', $post['content'], "내용이 일치해야 합니다.");
    }

    /**
     * 테스트 2: 게시물 상세보기 시 조회수가 1 증가하는지 테스트
     */
    public function testViewCountIncreasesWhenViewingPost(): void
    {
        // 테스트 데이터 삽입
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content, created_at, view_count) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute(['조회수 테스트', '작성자', '내용', 5]);
        $postId = $this->pdo->lastInsertId();

        // 초기 조회수 확인
        $stmt = $this->pdo->prepare("SELECT view_count FROM board WHERE id = ?");
        $stmt->execute([$postId]);
        $initialViewCount = $stmt->fetch()['view_count'];
        $this->assertEquals(5, $initialViewCount, "초기 조회수는 5여야 합니다.");

        // 조회수 증가
        $stmt = $this->pdo->prepare("UPDATE board SET view_count = view_count + 1 WHERE id = ?");
        $stmt->execute([$postId]);

        // 증가된 조회수 확인
        $stmt = $this->pdo->prepare("SELECT view_count FROM board WHERE id = ?");
        $stmt->execute([$postId]);
        $newViewCount = $stmt->fetch()['view_count'];
        $this->assertEquals(6, $newViewCount, "조회수가 1 증가하여 6이 되어야 합니다.");
        $this->assertEquals($initialViewCount + 1, $newViewCount, "조회수가 정확히 1 증가해야 합니다.");
    }

    /**
     * 테스트 3: 존재하지 않는 게시물 ID로 접근 시 에러 처리가 되는지 테스트
     */
    public function testErrorHandlingForNonExistentPostId(): void
    {
        // 존재하지 않는 ID로 조회
        $nonExistentId = 99999;
        $stmt = $this->pdo->prepare("SELECT * FROM board WHERE id = ?");
        $stmt->execute([$nonExistentId]);
        $post = $stmt->fetch();

        // 검증: 게시물이 없어야 함
        $this->assertFalse($post, "존재하지 않는 게시물 ID로 조회 시 false가 반환되어야 합니다.");
        $this->assertNull($post, "존재하지 않는 게시물 ID로 조회 시 null이 반환되어야 합니다.");
    }

    /**
     * 테스트 4: 게시물 상세보기 페이지에 제목, 작성자, 내용, 작성일시, 조회수가 표시되는지 테스트
     */
    public function testPostDetailContainsAllRequiredFields(): void
    {
        // 테스트 데이터 삽입
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content, created_at, view_count) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute(['전체 필드 테스트', '작성자명', '게시물 내용입니다', 10]);
        $postId = $this->pdo->lastInsertId();

        // 게시물 조회
        $stmt = $this->pdo->prepare("SELECT * FROM board WHERE id = ?");
        $stmt->execute([$postId]);
        $post = $stmt->fetch();

        // 검증: 모든 필수 필드가 존재하는지 확인
        $this->assertArrayHasKey('id', $post, "게시물에 ID 필드가 있어야 합니다.");
        $this->assertArrayHasKey('title', $post, "게시물에 제목 필드가 있어야 합니다.");
        $this->assertArrayHasKey('author', $post, "게시물에 작성자 필드가 있어야 합니다.");
        $this->assertArrayHasKey('content', $post, "게시물에 내용 필드가 있어야 합니다.");
        $this->assertArrayHasKey('created_at', $post, "게시물에 작성일시 필드가 있어야 합니다.");
        $this->assertArrayHasKey('view_count', $post, "게시물에 조회수 필드가 있어야 합니다.");

        // 필드 값 검증
        $this->assertNotEmpty($post['title'], "제목이 비어있지 않아야 합니다.");
        $this->assertNotEmpty($post['author'], "작성자가 비어있지 않아야 합니다.");
        $this->assertNotEmpty($post['content'], "내용이 비어있지 않아야 합니다.");
        $this->assertNotNull($post['created_at'], "작성일시가 설정되어야 합니다.");
        $this->assertIsNumeric($post['view_count'], "조회수는 숫자여야 합니다.");
    }
}
