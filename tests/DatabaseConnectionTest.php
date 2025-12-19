<?php
/**
 * 데이터베이스 연결 테스트
 * TDD RED 단계: 실패하는 테스트 작성
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config.php';

class DatabaseConnectionTest extends TestCase
{
    /**
     * 테스트 1: 데이터베이스 연결이 정상적으로 이루어지는지 테스트
     */
    public function testDatabaseConnectionIsSuccessful(): void
    {
        try {
            $pdo = getDBConnection();
            $this->assertInstanceOf(PDO::class, $pdo, "PDO 객체가 반환되어야 합니다.");
            
            // 연결 테스트 쿼리 실행
            $stmt = $pdo->query("SELECT 1");
            $result = $stmt->fetch();
            $this->assertEquals(1, $result[1], "데이터베이스 연결이 정상적으로 작동해야 합니다.");
        } catch (PDOException $e) {
            $this->fail("데이터베이스 연결 실패: " . $e->getMessage());
        }
    }

    /**
     * 테스트 2: 잘못된 데이터베이스 설정 시 에러 처리가 되는지 테스트
     */
    public function testErrorHandlingForInvalidDatabaseConfig(): void
    {
        // 잘못된 데이터베이스 설정으로 연결 시도
        try {
            $dsn = "mysql:host=localhost;dbname=nonexistent_db;charset=utf8mb4";
            $pdo = new PDO($dsn, 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 연결은 성공할 수 있지만, 쿼리 실행 시 에러 발생
            $pdo->query("SELECT 1");
            $this->fail("존재하지 않는 데이터베이스에 연결 시 예외가 발생해야 합니다.");
        } catch (PDOException $e) {
            $this->assertNotEmpty($e->getMessage(), "에러 메시지가 있어야 합니다.");
        }
    }

    /**
     * 테스트 3: PDO Prepared Statement가 정상적으로 동작하는지 테스트
     */
    public function testPDOPreparedStatementWorks(): void
    {
        try {
            $pdo = getDBConnection();
            
            // Prepared Statement 테스트
            $stmt = $pdo->prepare("SELECT ? as test_value");
            $stmt->execute([123]);
            $result = $stmt->fetch();
            
            $this->assertNotFalse($result, "Prepared Statement가 정상적으로 실행되어야 합니다.");
            $this->assertEquals(123, $result['test_value'], "바인딩된 값이 올바르게 반환되어야 합니다.");
        } catch (PDOException $e) {
            $this->fail("Prepared Statement 실행 실패: " . $e->getMessage());
        }
    }
}
