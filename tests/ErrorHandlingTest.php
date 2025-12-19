<?php
/**
 * 에러 처리 테스트
 * TDD RED 단계: 실패하는 테스트 작성
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config.php';

class ErrorHandlingTest extends TestCase
{
    /**
     * 테스트 1: 데이터베이스 연결 실패 시 적절한 에러 메시지가 표시되는지 테스트
     */
    public function testErrorMessageOnDatabaseConnectionFailure(): void
    {
        // 잘못된 설정으로 연결 시도
        try {
            $dsn = "mysql:host=invalid_host;dbname=invalid_db;charset=utf8mb4";
            $pdo = new PDO($dsn, 'invalid_user', 'invalid_pass');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->query("SELECT 1");
            $this->fail("잘못된 데이터베이스 설정으로 연결 시 예외가 발생해야 합니다.");
        } catch (PDOException $e) {
            $errorMessage = $e->getMessage();
            $this->assertNotEmpty($errorMessage, "에러 메시지가 있어야 합니다.");
            $this->assertIsString($errorMessage, "에러 메시지는 문자열이어야 합니다.");
        }
    }

    /**
     * 테스트 2: SQL 쿼리 실행 실패 시 예외 처리가 되는지 테스트
     */
    public function testExceptionHandlingOnSQLQueryFailure(): void
    {
        try {
            $pdo = getDBConnection();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 잘못된 SQL 쿼리 실행
            try {
                $pdo->query("SELECT * FROM nonexistent_table");
                $this->fail("존재하지 않는 테이블 조회 시 예외가 발생해야 합니다.");
            } catch (PDOException $e) {
                $this->assertNotEmpty($e->getMessage(), "SQL 에러 메시지가 있어야 합니다.");
                $this->assertStringContainsString('nonexistent_table', $e->getMessage(), "에러 메시지에 테이블명이 포함되어야 합니다.");
            }
        } catch (PDOException $e) {
            $this->markTestSkipped("데이터베이스 연결 실패: " . $e->getMessage());
        }
    }

    /**
     * 테스트 3: 잘못된 파라미터 전달 시 에러 처리가 되는지 테스트
     */
    public function testErrorHandlingForInvalidParameters(): void
    {
        try {
            $pdo = getDBConnection();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 잘못된 타입의 파라미터 전달
            try {
                $stmt = $pdo->prepare("SELECT * FROM board WHERE id = ?");
                // 배열 대신 문자열 전달 (실제로는 작동할 수 있지만, 타입 검증 테스트)
                $stmt->execute(['invalid_id']);
                
                // 결과 확인
                $result = $stmt->fetch();
                // 숫자가 아닌 문자열로 조회하면 결과가 없어야 함
                $this->assertFalse($result, "잘못된 파라미터로 조회 시 결과가 없어야 합니다.");
            } catch (PDOException $e) {
                // 예외 발생도 정상적인 에러 처리
                $this->assertNotEmpty($e->getMessage());
            }
        } catch (PDOException $e) {
            $this->markTestSkipped("데이터베이스 연결 실패: " . $e->getMessage());
        }
    }

    /**
     * 테스트 4: NULL 값 처리 테스트
     */
    public function testNullValueHandling(): void
    {
        try {
            $pdo = getDBConnection();
            $pdo->exec("TRUNCATE TABLE board");
            
            // NULL 값이 허용되지 않는 필드에 NULL 전달
            try {
                $stmt = $pdo->prepare("INSERT INTO board (title, author, content) VALUES (?, ?, ?)");
                $result = $stmt->execute([null, '작성자', '내용']);
                $this->fail("NOT NULL 필드에 NULL 값 전달 시 예외가 발생해야 합니다.");
            } catch (PDOException $e) {
                $this->assertNotEmpty($e->getMessage(), "NULL 값 에러 메시지가 있어야 합니다.");
            }
        } catch (PDOException $e) {
            $this->markTestSkipped("데이터베이스 연결 실패: " . $e->getMessage());
        }
    }
}
