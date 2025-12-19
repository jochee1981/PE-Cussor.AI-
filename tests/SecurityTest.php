<?php
/**
 * 보안 기능 테스트
 * TDD RED 단계: 실패하는 테스트 작성
 */

use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    /**
     * 테스트 1: XSS 공격 방지: <script> 태그가 포함된 입력값이 이스케이프 처리되는지 테스트
     */
    public function testXSSPreventionWithScriptTag(): void
    {
        $maliciousInput = '<script>alert("XSS")</script>';
        $escaped = htmlspecialchars($maliciousInput, ENT_QUOTES, 'UTF-8');
        
        // 검증: <script> 태그가 이스케이프되어야 함
        $this->assertStringNotContainsString('<script>', $escaped, "<script> 태그가 이스케이프되어야 합니다.");
        $this->assertStringContainsString('&lt;script&gt;', $escaped, "&lt;script&gt;로 변환되어야 합니다.");
        $this->assertStringNotContainsString('alert', $escaped, "스크립트 내용이 실행되지 않도록 이스케이프되어야 합니다.");
    }

    /**
     * 테스트 2: SQL Injection 방지: 악의적인 SQL 코드가 실행되지 않는지 테스트
     */
    public function testSQLInjectionPrevention(): void
    {
        require_once __DIR__ . '/../config.php';
        
        try {
            $pdo = getDBConnection();
            $pdo->exec("TRUNCATE TABLE board");
            
            // SQL Injection 시도
            $maliciousInput = "'; DROP TABLE board; --";
            
            // Prepared Statement 사용 (SQL Injection 방지)
            $stmt = $pdo->prepare("INSERT INTO board (title, author, content) VALUES (?, ?, ?)");
            $result = $stmt->execute(['테스트', $maliciousInput, '내용']);
            $postId = $pdo->lastInsertId();
            
            // 검증: 테이블이 삭제되지 않았고, 입력값이 문자열로 저장되었는지 확인
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM board");
            $count = $stmt->fetch()['count'];
            $this->assertGreaterThan(0, $count, "테이블이 삭제되지 않아야 합니다.");
            
            $stmt = $pdo->prepare("SELECT author FROM board WHERE id = ?");
            $stmt->execute([$postId]);
            $savedAuthor = $stmt->fetch()['author'];
            $this->assertEquals($maliciousInput, $savedAuthor, "악의적인 입력이 문자열로 저장되어야 합니다.");
            
            $pdo->exec("TRUNCATE TABLE board");
        } catch (PDOException $e) {
            $this->fail("SQL Injection 테스트 실패: " . $e->getMessage());
        }
    }

    /**
     * 테스트 3: HTML 특수문자(<, >, &, ", ')가 htmlspecialchars()로 처리되는지 테스트
     */
    public function testHTMLSpecialCharsEscaping(): void
    {
        $testCases = [
            '<' => '&lt;',
            '>' => '&gt;',
            '&' => '&amp;',
            '"' => '&quot;',
            "'" => '&#039;'
        ];
        
        foreach ($testCases as $input => $expected) {
            $escaped = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            $this->assertEquals($expected, $escaped, "{$input}가 {$expected}로 이스케이프되어야 합니다.");
        }
        
        // 복합 테스트
        $complexInput = '<script>alert("XSS & Attack")</script>';
        $escaped = htmlspecialchars($complexInput, ENT_QUOTES, 'UTF-8');
        $this->assertStringNotContainsString('<script>', $escaped, "복합 입력도 이스케이프되어야 합니다.");
        $this->assertStringNotContainsString('alert', $escaped, "스크립트 내용이 이스케이프되어야 합니다.");
    }
}
