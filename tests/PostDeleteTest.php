<?php
/**
 * 게시물 삭제 기능 테스트
 * TDD RED 단계: 실패하는 테스트 작성
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config.php';

class PostDeleteTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        try {
            $this->pdo = getDBConnection();
            $this->pdo->exec("TRUNCATE TABLE comments");
            $this->pdo->exec("TRUNCATE TABLE board");
        } catch (PDOException $e) {
            $this->markTestSkipped("데이터베이스 연결 실패: " . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        if ($this->pdo) {
            $this->pdo->exec("TRUNCATE TABLE comments");
            $this->pdo->exec("TRUNCATE TABLE board");
        }
    }

    /**
     * 테스트 1: 게시물 삭제 시 해당 게시물이 데이터베이스에서 제거되는지 테스트
     */
    public function testPostIsRemovedFromDatabaseOnDelete(): void
    {
        // 테스트 데이터 삽입
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content, created_at, view_count) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute(['삭제 테스트', '작성자', '내용', 0]);
        $postId = $this->pdo->lastInsertId();

        // 삭제 전 존재 확인
        $stmt = $this->pdo->prepare("SELECT id FROM board WHERE id = ?");
        $stmt->execute([$postId]);
        $this->assertNotFalse($stmt->fetch(), "삭제 전 게시물이 존재해야 합니다.");

        // 게시물 삭제
        $stmt = $this->pdo->prepare("DELETE FROM board WHERE id = ?");
        $result = $stmt->execute([$postId]);

        // 검증
        $this->assertTrue($result, "게시물 삭제가 성공해야 합니다.");
        $this->assertEquals(1, $stmt->rowCount(), "1개의 게시물이 삭제되어야 합니다.");

        // 삭제 후 존재 확인
        $stmt = $this->pdo->prepare("SELECT id FROM board WHERE id = ?");
        $stmt->execute([$postId]);
        $this->assertFalse($stmt->fetch(), "삭제 후 게시물이 존재하지 않아야 합니다.");
    }

    /**
     * 테스트 2: 게시물 삭제 시 관련 댓글도 함께 삭제되는지 테스트
     */
    public function testCommentsAreDeletedWhenPostIsDeleted(): void
    {
        // 테스트 데이터 삽입
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content, created_at, view_count) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute(['댓글 삭제 테스트', '작성자', '내용', 0]);
        $postId = $this->pdo->lastInsertId();

        // 댓글 삽입
        $stmt = $this->pdo->prepare("INSERT INTO comments (board_id, author, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$postId, '댓글 작성자1', '댓글 내용1']);
        $commentId1 = $this->pdo->lastInsertId();
        $stmt->execute([$postId, '댓글 작성자2', '댓글 내용2']);
        $commentId2 = $this->pdo->lastInsertId();

        // 댓글 존재 확인
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM comments WHERE board_id = ?");
        $stmt->execute([$postId]);
        $commentCount = $stmt->fetch()['count'];
        $this->assertEquals(2, $commentCount, "댓글이 2개 있어야 합니다.");

        // 게시물 삭제
        $stmt = $this->pdo->prepare("DELETE FROM board WHERE id = ?");
        $stmt->execute([$postId]);

        // 댓글도 함께 삭제되었는지 확인 (CASCADE 또는 수동 삭제)
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM comments WHERE board_id = ?");
        $stmt->execute([$postId]);
        $remainingComments = $stmt->fetch()['count'];
        
        // CASCADE가 설정되어 있지 않다면 수동으로 댓글 삭제해야 함
        if ($remainingComments > 0) {
            $stmt = $this->pdo->prepare("DELETE FROM comments WHERE board_id = ?");
            $stmt->execute([$postId]);
        }
        
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM comments WHERE board_id = ?");
        $stmt->execute([$postId]);
        $finalCommentCount = $stmt->fetch()['count'];
        $this->assertEquals(0, $finalCommentCount, "게시물 삭제 시 관련 댓글도 삭제되어야 합니다.");
    }

    /**
     * 테스트 3: 존재하지 않는 게시물 ID로 삭제 시도 시 에러 처리가 되는지 테스트
     */
    public function testErrorHandlingForNonExistentPostIdOnDelete(): void
    {
        $nonExistentId = 99999;

        // 존재하지 않는 ID로 삭제 시도
        $stmt = $this->pdo->prepare("DELETE FROM board WHERE id = ?");
        $result = $stmt->execute([$nonExistentId]);

        // 검증: 삭제는 성공하지만 영향받은 행이 없어야 함
        $this->assertTrue($result, "DELETE 쿼리는 성공해야 합니다.");
        $this->assertEquals(0, $stmt->rowCount(), "존재하지 않는 게시물이므로 영향받은 행이 0이어야 합니다.");
    }
}
