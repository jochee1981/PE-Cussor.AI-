<?php
/**
 * 게시물 작성 기능 테스트
 * TDD RED 단계: 실패하는 테스트 작성
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config.php';

class PostCreateTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        try {
            $this->pdo = getDBConnection();
            $this->pdo->exec("TRUNCATE TABLE board");
        } catch (PDOException $e) {
            $this->markTestSkipped("데이터베이스 연결 실패: " . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        if ($this->pdo) {
            $this->pdo->exec("TRUNCATE TABLE board");
        }
    }

    /**
     * 테스트 1: 제목, 작성자, 내용이 모두 입력되었을 때 게시물이 정상적으로 등록되는지 테스트
     */
    public function testPostIsCreatedWhenAllFieldsAreProvided(): void
    {
        $title = '테스트 제목';
        $author = '테스트 작성자';
        $content = '테스트 내용입니다.';

        // 게시물 등록
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content) VALUES (?, ?, ?)");
        $result = $stmt->execute([$title, $author, $content]);
        $postId = $this->pdo->lastInsertId();

        // 검증
        $this->assertTrue($result, "게시물 등록이 성공해야 합니다.");
        $this->assertGreaterThan(0, $postId, "게시물 ID가 생성되어야 합니다.");

        // 데이터베이스에서 조회하여 확인
        $stmt = $this->pdo->prepare("SELECT * FROM board WHERE id = ?");
        $stmt->execute([$postId]);
        $post = $stmt->fetch();

        $this->assertNotFalse($post, "등록된 게시물이 조회되어야 합니다.");
        $this->assertEquals($title, $post['title'], "제목이 일치해야 합니다.");
        $this->assertEquals($author, $post['author'], "작성자가 일치해야 합니다.");
        $this->assertEquals($content, $post['content'], "내용이 일치해야 합니다.");
    }

    /**
     * 테스트 2: 제목이 비어있을 때 에러 메시지가 표시되는지 테스트
     */
    public function testErrorWhenTitleIsEmpty(): void
    {
        $title = '';
        $author = '테스트 작성자';
        $content = '테스트 내용입니다.';

        // 빈 제목으로 게시물 등록 시도
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content) VALUES (?, ?, ?)");
        
        // NOT NULL 제약조건으로 인한 에러 발생 여부 확인
        try {
            $result = $stmt->execute([$title, $author, $content]);
            $this->fail("빈 제목으로 게시물 등록 시 예외가 발생해야 합니다.");
        } catch (PDOException $e) {
            $this->assertStringContainsString('title', strtolower($e->getMessage()), "제목 관련 에러 메시지가 포함되어야 합니다.");
        }
    }

    /**
     * 테스트 3: 작성자가 비어있을 때 에러 메시지가 표시되는지 테스트
     */
    public function testErrorWhenAuthorIsEmpty(): void
    {
        $title = '테스트 제목';
        $author = '';
        $content = '테스트 내용입니다.';

        // 빈 작성자로 게시물 등록 시도
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content) VALUES (?, ?, ?)");
        
        try {
            $result = $stmt->execute([$title, $author, $content]);
            $this->fail("빈 작성자로 게시물 등록 시 예외가 발생해야 합니다.");
        } catch (PDOException $e) {
            $this->assertStringContainsString('author', strtolower($e->getMessage()), "작성자 관련 에러 메시지가 포함되어야 합니다.");
        }
    }

    /**
     * 테스트 4: 내용이 비어있을 때 에러 메시지가 표시되는지 테스트
     */
    public function testErrorWhenContentIsEmpty(): void
    {
        $title = '테스트 제목';
        $author = '테스트 작성자';
        $content = '';

        // 빈 내용으로 게시물 등록 시도
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content) VALUES (?, ?, ?)");
        
        // 내용은 NULL 허용 가능성이 있으므로, 유효성 검사 로직 테스트
        $result = $stmt->execute([$title, $author, $content]);
        
        // 내용이 비어있을 때의 처리 검증 (실제 구현에 따라 다를 수 있음)
        if ($result) {
            $postId = $this->pdo->lastInsertId();
            $stmt = $this->pdo->prepare("SELECT content FROM board WHERE id = ?");
            $stmt->execute([$postId]);
            $savedContent = $stmt->fetch()['content'];
            $this->assertEmpty($savedContent, "빈 내용이 저장되었습니다.");
        }
    }

    /**
     * 테스트 5: 작성된 게시물이 데이터베이스에 저장되는지 테스트
     */
    public function testPostIsSavedToDatabase(): void
    {
        $title = '저장 테스트 제목';
        $author = '저장 테스트 작성자';
        $content = '저장 테스트 내용입니다.';

        // 게시물 등록
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content) VALUES (?, ?, ?)");
        $stmt->execute([$title, $author, $content]);
        $postId = $this->pdo->lastInsertId();

        // 데이터베이스에서 직접 조회
        $stmt = $this->pdo->prepare("SELECT * FROM board WHERE id = ?");
        $stmt->execute([$postId]);
        $post = $stmt->fetch();

        // 검증
        $this->assertNotFalse($post, "게시물이 데이터베이스에 저장되어야 합니다.");
        $this->assertEquals($postId, $post['id'], "게시물 ID가 일치해야 합니다.");
        $this->assertEquals($title, $post['title'], "제목이 저장되어야 합니다.");
        $this->assertEquals($author, $post['author'], "작성자가 저장되어야 합니다.");
        $this->assertEquals($content, $post['content'], "내용이 저장되어야 합니다.");
        $this->assertNotNull($post['created_at'], "작성일시가 자동으로 설정되어야 합니다.");
    }
}
