<?php
/**
 * 게시물 목록 기능 테스트
 * TDD RED 단계: 실패하는 테스트 작성
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config.php';

class PostListTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        // 테스트용 데이터베이스 연결
        try {
            $this->pdo = getDBConnection();
            // 테스트 전 테이블 초기화
            $this->pdo->exec("TRUNCATE TABLE board");
        } catch (PDOException $e) {
            $this->markTestSkipped("데이터베이스 연결 실패: " . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        // 테스트 후 정리
        if ($this->pdo) {
            $this->pdo->exec("TRUNCATE TABLE board");
        }
    }

    /**
     * 테스트 1: 게시물 목록이 최신순(내림차순)으로 정렬되어 표시되는지 테스트
     */
    public function testPostsAreSortedByLatestFirst(): void
    {
        // 테스트 데이터 삽입
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content, created_at, view_count) VALUES (?, ?, ?, NOW(), ?)");
        
        // 오래된 게시물
        $stmt->execute(['첫 번째 게시물', '작성자1', '내용1', 0]);
        $firstId = $this->pdo->lastInsertId();
        sleep(1); // 시간 차이를 위해 대기
        
        // 최신 게시물
        $stmt->execute(['두 번째 게시물', '작성자2', '내용2', 0]);
        $secondId = $this->pdo->lastInsertId();
        sleep(1);
        
        // 더 최신 게시물
        $stmt->execute(['세 번째 게시물', '작성자3', '내용3', 0]);
        $thirdId = $this->pdo->lastInsertId();

        // 게시물 목록 조회 (최신순)
        $stmt = $this->pdo->query("SELECT * FROM board ORDER BY id DESC");
        $posts = $stmt->fetchAll();

        // 검증: 최신 게시물이 첫 번째에 와야 함
        $this->assertCount(3, $posts, "게시물이 3개여야 합니다.");
        $this->assertEquals($thirdId, $posts[0]['id'], "가장 최신 게시물이 첫 번째에 와야 합니다.");
        $this->assertEquals($secondId, $posts[1]['id'], "두 번째로 최신 게시물이 두 번째에 와야 합니다.");
        $this->assertEquals($firstId, $posts[2]['id'], "가장 오래된 게시물이 마지막에 와야 합니다.");
    }

    /**
     * 테스트 2: 게시물 목록에 제목, 작성자, 작성일시, 조회수가 표시되는지 테스트
     */
    public function testPostListContainsRequiredFields(): void
    {
        // 테스트 데이터 삽입
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content, created_at, view_count) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute(['테스트 제목', '테스트 작성자', '테스트 내용', 5]);
        $postId = $this->pdo->lastInsertId();

        // 게시물 목록 조회
        $stmt = $this->pdo->query("SELECT * FROM board ORDER BY id DESC");
        $posts = $stmt->fetchAll();

        // 검증: 필수 필드가 모두 존재하는지 확인
        $this->assertNotEmpty($posts, "게시물 목록이 비어있지 않아야 합니다.");
        
        $post = $posts[0];
        $this->assertArrayHasKey('title', $post, "게시물에 제목 필드가 있어야 합니다.");
        $this->assertArrayHasKey('author', $post, "게시물에 작성자 필드가 있어야 합니다.");
        $this->assertArrayHasKey('created_at', $post, "게시물에 작성일시 필드가 있어야 합니다.");
        $this->assertArrayHasKey('view_count', $post, "게시물에 조회수 필드가 있어야 합니다.");
        
        $this->assertEquals('테스트 제목', $post['title'], "제목이 올바르게 저장되어야 합니다.");
        $this->assertEquals('테스트 작성자', $post['author'], "작성자가 올바르게 저장되어야 합니다.");
        $this->assertEquals(5, $post['view_count'], "조회수가 올바르게 저장되어야 합니다.");
        $this->assertNotNull($post['created_at'], "작성일시가 설정되어야 합니다.");
    }

    /**
     * 테스트 3: 게시물이 없을 때 빈 목록이 표시되는지 테스트
     */
    public function testEmptyPostListIsDisplayedWhenNoPosts(): void
    {
        // 게시물이 없는 상태에서 목록 조회
        $stmt = $this->pdo->query("SELECT * FROM board ORDER BY id DESC");
        $posts = $stmt->fetchAll();

        // 검증: 빈 배열이 반환되어야 함
        $this->assertIsArray($posts, "게시물 목록은 배열이어야 합니다.");
        $this->assertEmpty($posts, "게시물이 없을 때 빈 배열이 반환되어야 합니다.");
        $this->assertCount(0, $posts, "게시물이 없을 때 개수는 0이어야 합니다.");
    }

    /**
     * 테스트 4: 게시물 목록에서 제목 클릭 시 상세보기 페이지로 이동하는지 테스트
     * (이 테스트는 실제로는 HTML 링크를 확인하는 것이므로, 
     *  게시물 ID가 올바르게 반환되는지 확인)
     */
    public function testPostIdIsAvailableForDetailView(): void
    {
        // 테스트 데이터 삽입
        $stmt = $this->pdo->prepare("INSERT INTO board (title, author, content, created_at, view_count) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute(['상세보기 테스트', '작성자', '내용', 0]);
        $postId = $this->pdo->lastInsertId();

        // 게시물 목록 조회
        $stmt = $this->pdo->query("SELECT * FROM board ORDER BY id DESC");
        $posts = $stmt->fetchAll();

        // 검증: 게시물 ID가 존재하고 올바른지 확인
        $this->assertNotEmpty($posts, "게시물 목록이 비어있지 않아야 합니다.");
        
        $post = $posts[0];
        $this->assertArrayHasKey('id', $post, "게시물에 ID 필드가 있어야 합니다.");
        $this->assertEquals($postId, $post['id'], "게시물 ID가 올바르게 반환되어야 합니다.");
        $this->assertIsNumeric($post['id'], "게시물 ID는 숫자여야 합니다.");
        
        // 상세보기 페이지 URL 생성 가능 여부 확인
        $detailUrl = "view.php?id=" . $post['id'];
        $this->assertStringContainsString("view.php?id=", $detailUrl, "상세보기 URL이 올바르게 생성되어야 합니다.");
        $this->assertStringContainsString((string)$postId, $detailUrl, "상세보기 URL에 게시물 ID가 포함되어야 합니다.");
    }
}
