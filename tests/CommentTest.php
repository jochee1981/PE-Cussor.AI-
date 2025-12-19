<?php
/**
 * 댓글 기능 테스트
 * TDD RED 단계: 실패하는 테스트 작성
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config.php';

class CommentTest extends TestCase
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
     * 테스트 1: 댓글이 정상적으로 등록되는지 테스트
     */
    public function testCommentIsCreatedSuccessfully(): void
    {
        // 게시물 생성
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content, created_at, view_count) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute(['댓글 테스트 게시물', '게시물 작성자', '게시물 내용', 0]);
        $boardId = $this->pdo->lastInsertId();

        // 댓글 등록
        $author = '댓글 작성자';
        $content = '댓글 내용입니다.';
        
        $stmt = $this->pdo->prepare("INSERT INTO comments (board_id, author, content, created_at) VALUES (?, ?, ?, NOW())");
        $result = $stmt->execute([$boardId, $author, $content]);
        $commentId = $this->pdo->lastInsertId();

        // 검증
        $this->assertTrue($result, "댓글 등록이 성공해야 합니다.");
        $this->assertGreaterThan(0, $commentId, "댓글 ID가 생성되어야 합니다.");

        // 데이터베이스에서 조회하여 확인
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE id = ?");
        $stmt->execute([$commentId]);
        $comment = $stmt->fetch();

        $this->assertNotFalse($comment, "등록된 댓글이 조회되어야 합니다.");
        $this->assertEquals($boardId, $comment['board_id'], "게시물 ID가 일치해야 합니다.");
        $this->assertEquals($author, $comment['author'], "댓글 작성자가 일치해야 합니다.");
        $this->assertEquals($content, $comment['content'], "댓글 내용이 일치해야 합니다.");
    }

    /**
     * 테스트 2: 댓글 작성자와 내용이 비어있을 때 에러 메시지가 표시되는지 테스트
     */
    public function testErrorWhenCommentAuthorOrContentIsEmpty(): void
    {
        // 게시물 생성
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content, created_at, view_count) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute(['댓글 테스트 게시물', '게시물 작성자', '게시물 내용', 0]);
        $boardId = $this->pdo->lastInsertId();

        // 빈 작성자로 댓글 등록 시도
        try {
            $stmt = $this->pdo->prepare("INSERT INTO comments (board_id, author, content, created_at) VALUES (?, ?, ?, NOW())");
            $result = $stmt->execute([$boardId, '', '댓글 내용']);
            $this->fail("빈 작성자로 댓글 등록 시 예외가 발생해야 합니다.");
        } catch (PDOException $e) {
            $this->assertStringContainsString('author', strtolower($e->getMessage()), "작성자 관련 에러 메시지가 포함되어야 합니다.");
        }
    }

    /**
     * 테스트 3: 특정 게시물의 댓글 목록이 최신순으로 표시되는지 테스트
     */
    public function testCommentsAreSortedByLatestFirst(): void
    {
        // 게시물 생성
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content, created_at, view_count) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute(['댓글 정렬 테스트', '작성자', '내용', 0]);
        $boardId = $this->pdo->lastInsertId();

        // 댓글 여러 개 삽입
        $stmt = $this->pdo->prepare("INSERT INTO comments (board_id, author, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$boardId, '작성자1', '첫 번째 댓글']);
        $firstCommentId = $this->pdo->lastInsertId();
        sleep(1);
        
        $stmt->execute([$boardId, '작성자2', '두 번째 댓글']);
        $secondCommentId = $this->pdo->lastInsertId();
        sleep(1);
        
        $stmt->execute([$boardId, '작성자3', '세 번째 댓글']);
        $thirdCommentId = $this->pdo->lastInsertId();

        // 댓글 목록 조회 (최신순)
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE board_id = ? ORDER BY created_at DESC");
        $stmt->execute([$boardId]);
        $comments = $stmt->fetchAll();

        // 검증: 최신 댓글이 첫 번째에 와야 함
        $this->assertCount(3, $comments, "댓글이 3개여야 합니다.");
        $this->assertEquals($thirdCommentId, $comments[0]['id'], "가장 최신 댓글이 첫 번째에 와야 합니다.");
        $this->assertEquals($secondCommentId, $comments[1]['id'], "두 번째로 최신 댓글이 두 번째에 와야 합니다.");
        $this->assertEquals($firstCommentId, $comments[2]['id'], "가장 오래된 댓글이 마지막에 와야 합니다.");
    }

    /**
     * 테스트 4: 댓글이 데이터베이스에 저장되는지 테스트
     */
    public function testCommentIsSavedToDatabase(): void
    {
        // 게시물 생성
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content, created_at, view_count) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute(['댓글 저장 테스트', '게시물 작성자', '게시물 내용', 0]);
        $boardId = $this->pdo->lastInsertId();

        // 댓글 등록
        $author = '댓글 작성자';
        $content = '저장 테스트 댓글 내용입니다.';
        
        $stmt = $this->pdo->prepare("INSERT INTO comments (board_id, author, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$boardId, $author, $content]);
        $commentId = $this->pdo->lastInsertId();

        // 데이터베이스에서 직접 조회
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE id = ?");
        $stmt->execute([$commentId]);
        $comment = $stmt->fetch();

        // 검증
        $this->assertNotFalse($comment, "댓글이 데이터베이스에 저장되어야 합니다.");
        $this->assertEquals($commentId, $comment['id'], "댓글 ID가 일치해야 합니다.");
        $this->assertEquals($boardId, $comment['board_id'], "게시물 ID가 일치해야 합니다.");
        $this->assertEquals($author, $comment['author'], "댓글 작성자가 저장되어야 합니다.");
        $this->assertEquals($content, $comment['content'], "댓글 내용이 저장되어야 합니다.");
        $this->assertNotNull($comment['created_at'], "댓글 작성일시가 자동으로 설정되어야 합니다.");
    }
}
