<?php
/**
 * 게시물 수정 기능 테스트
 * TDD RED 단계: 실패하는 테스트 작성
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config.php';

class PostUpdateTest extends TestCase
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
     * 테스트 1: 기존 게시물의 내용이 수정 폼에 정상적으로 로드되는지 테스트
     */
    public function testExistingPostCanBeLoadedForEditing(): void
    {
        // 테스트 데이터 삽입
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content, created_at, view_count) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute(['원본 제목', '원본 작성자', '원본 내용', 5]);
        $postId = $this->pdo->lastInsertId();

        // 게시물 조회 (수정 폼 로드)
        $stmt = $this->pdo->prepare("SELECT * FROM board WHERE id = ?");
        $stmt->execute([$postId]);
        $post = $stmt->fetch();

        // 검증
        $this->assertNotFalse($post, "게시물이 조회되어야 합니다.");
        $this->assertEquals($postId, $post['id'], "게시물 ID가 일치해야 합니다.");
        $this->assertEquals('원본 제목', $post['title'], "원본 제목이 로드되어야 합니다.");
        $this->assertEquals('원본 작성자', $post['author'], "원본 작성자가 로드되어야 합니다.");
        $this->assertEquals('원본 내용', $post['content'], "원본 내용이 로드되어야 합니다.");
    }

    /**
     * 테스트 2: 게시물 수정 후 변경사항이 데이터베이스에 반영되는지 테스트
     */
    public function testPostUpdateReflectsInDatabase(): void
    {
        // 테스트 데이터 삽입
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content, created_at, view_count) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute(['수정 전 제목', '수정 전 작성자', '수정 전 내용', 0]);
        $postId = $this->pdo->lastInsertId();

        // 게시물 수정
        $newTitle = '수정 후 제목';
        $newAuthor = '수정 후 작성자';
        $newContent = '수정 후 내용';
        
        $stmt = $this->pdo->prepare("UPDATE board SET title = ?, author = ?, content = ? WHERE id = ?");
        $result = $stmt->execute([$newTitle, $newAuthor, $newContent, $postId]);

        // 검증
        $this->assertTrue($result, "게시물 수정이 성공해야 합니다.");

        // 수정된 내용 확인
        $stmt = $this->pdo->prepare("SELECT * FROM board WHERE id = ?");
        $stmt->execute([$postId]);
        $updatedPost = $stmt->fetch();

        $this->assertEquals($newTitle, $updatedPost['title'], "제목이 수정되어야 합니다.");
        $this->assertEquals($newAuthor, $updatedPost['author'], "작성자가 수정되어야 합니다.");
        $this->assertEquals($newContent, $updatedPost['content'], "내용이 수정되어야 합니다.");
    }

    /**
     * 테스트 3: 존재하지 않는 게시물 ID로 수정 시도 시 에러 처리가 되는지 테스트
     */
    public function testErrorHandlingForNonExistentPostIdOnUpdate(): void
    {
        $nonExistentId = 99999;
        $title = '수정 제목';
        $author = '수정 작성자';
        $content = '수정 내용';

        // 존재하지 않는 ID로 수정 시도
        $stmt = $this->pdo->prepare("UPDATE board SET title = ?, author = ?, content = ? WHERE id = ?");
        $result = $stmt->execute([$title, $author, $content, $nonExistentId]);

        // 검증: 수정은 성공하지만 영향받은 행이 없어야 함
        $this->assertTrue($result, "UPDATE 쿼리는 성공해야 합니다.");
        $this->assertEquals(0, $stmt->rowCount(), "존재하지 않는 게시물이므로 영향받은 행이 0이어야 합니다.");
    }

    /**
     * 테스트 4: 수정 시 필수 필드(제목, 작성자, 내용) 검증이 동작하는지 테스트
     */
    public function testRequiredFieldsValidationOnUpdate(): void
    {
        // 테스트 데이터 삽입
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content, created_at, view_count) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute(['원본 제목', '원본 작성자', '원본 내용', 0]);
        $postId = $this->pdo->lastInsertId();

        // 빈 제목으로 수정 시도
        try {
            $stmt = $this->pdo->prepare("UPDATE board SET title = ?, author = ?, content = ? WHERE id = ?");
            $result = $stmt->execute(['', '작성자', '내용', $postId]);
            // NOT NULL 제약조건으로 인한 에러 발생 여부 확인
            $this->fail("빈 제목으로 수정 시 예외가 발생해야 합니다.");
        } catch (PDOException $e) {
            $this->assertStringContainsString('title', strtolower($e->getMessage()), "제목 관련 에러 메시지가 포함되어야 합니다.");
        }
    }
}
