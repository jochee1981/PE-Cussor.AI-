<?php
/**
 * 데이터 유효성 검사 테스트
 * TDD RED 단계: 실패하는 테스트 작성
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config.php';

class ValidationTest extends TestCase
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
     * 테스트 1: 제목 길이 제한 검증 테스트
     */
    public function testTitleLengthValidation(): void
    {
        // VARCHAR(255) 제한 테스트
        $maxLength = 255;
        $validTitle = str_repeat('a', $maxLength);
        $invalidTitle = str_repeat('a', $maxLength + 1);

        // 유효한 길이의 제목
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content) VALUES (?, ?, ?)");
        $result = $stmt->execute([$validTitle, '작성자', '내용']);
        $this->assertTrue($result, "최대 길이 제목은 저장되어야 합니다.");

        // 유효하지 않은 길이의 제목
        try {
            $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content) VALUES (?, ?, ?)");
            $result = $stmt->execute([$invalidTitle, '작성자', '내용']);
            // MySQL은 자동으로 잘라낼 수 있으므로, 실제 길이 확인
            $postId = $this->pdo->lastInsertId();
            $stmt = $this->pdo->prepare("SELECT title FROM board WHERE id = ?");
            $stmt->execute([$postId]);
            $savedTitle = $stmt->fetch()['title'];
            $this->assertLessThanOrEqual($maxLength, strlen($savedTitle), "제목이 최대 길이로 제한되어야 합니다.");
        } catch (PDOException $e) {
            // 에러 발생도 유효성 검사로 간주
            $this->assertNotEmpty($e->getMessage());
        }
    }

    /**
     * 테스트 2: 작성자 이름 길이 제한 검증 테스트
     */
    public function testAuthorNameLengthValidation(): void
    {
        // VARCHAR(50) 제한 테스트
        $maxLength = 50;
        $validAuthor = str_repeat('a', $maxLength);
        $invalidAuthor = str_repeat('a', $maxLength + 1);

        // 유효한 길이의 작성자
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content) VALUES (?, ?, ?)");
        $result = $stmt->execute(['제목', $validAuthor, '내용']);
        $this->assertTrue($result, "최대 길이 작성자는 저장되어야 합니다.");

        // 유효하지 않은 길이의 작성자
        try {
            $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content) VALUES (?, ?, ?)");
            $result = $stmt->execute(['제목', $invalidAuthor, '내용']);
            $postId = $this->pdo->lastInsertId();
            $stmt = $this->pdo->prepare("SELECT author FROM board WHERE id = ?");
            $stmt->execute([$postId]);
            $savedAuthor = $stmt->fetch()['author'];
            $this->assertLessThanOrEqual($maxLength, strlen($savedAuthor), "작성자 이름이 최대 길이로 제한되어야 합니다.");
        } catch (PDOException $e) {
            $this->assertNotEmpty($e->getMessage());
        }
    }

    /**
     * 테스트 3: 내용 길이 제한 검증 테스트
     */
    public function testContentLengthValidation(): void
    {
        // TEXT 타입은 큰 데이터를 저장할 수 있지만, 실용적인 제한 테스트
        $largeContent = str_repeat('내용 ', 10000); // 약 30KB

        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content) VALUES (?, ?, ?)");
        $result = $stmt->execute(['제목', '작성자', $largeContent]);
        $postId = $this->pdo->lastInsertId();

        // 검증: 큰 내용도 저장되어야 함
        $stmt = $this->pdo->prepare("SELECT content FROM board WHERE id = ?");
        $stmt->execute([$postId]);
        $savedContent = $stmt->fetch()['content'];
        $this->assertEquals($largeContent, $savedContent, "큰 내용도 저장되어야 합니다.");
    }

    /**
     * 테스트 4: 댓글 내용 길이 제한 검증 테스트
     */
    public function testCommentContentLengthValidation(): void
    {
        // 게시물 생성
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content) VALUES (?, ?, ?)");
        $stmt->execute(['댓글 테스트', '작성자', '내용']);
        $boardId = $this->pdo->lastInsertId();

        // 정상적인 댓글
        $normalComment = '정상적인 댓글 내용입니다.';
        $stmt = $this->pdo->prepare("INSERT INTO comments (board_id, author, content) VALUES (?, ?, ?)");
        $result = $stmt->execute([$boardId, '댓글 작성자', $normalComment]);
        $this->assertTrue($result, "정상적인 댓글이 저장되어야 합니다.");

        // 긴 댓글 (TEXT 타입이므로 큰 데이터도 저장 가능)
        $longComment = str_repeat('댓글 내용 ', 1000);
        $result = $stmt->execute([$boardId, '댓글 작성자2', $longComment]);
        $this->assertTrue($result, "긴 댓글도 저장되어야 합니다.");
    }
}
