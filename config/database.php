<?php
/**
 * 데이터베이스 설정 클래스
 */
class DatabaseConfig {
    const HOST = 'localhost';
    const NAME = 'cursor_db';
    const USER = 'root';
    const PASS = '';
    const CHARSET = 'utf8mb4';
    
    /**
     * PDO 연결 생성
     * @return PDO
     * @throws PDOException
     */
    public static function getConnection(): PDO {
        try {
            $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::NAME . ";charset=" . self::CHARSET;
            $pdo = new PDO($dsn, self::USER, self::PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch (PDOException $e) {
            throw new PDOException("데이터베이스 연결 실패: " . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * DB 생성 전 연결 (DB명 없이)
     * @return PDO
     * @throws PDOException
     */
    public static function getConnectionWithoutDB(): PDO {
        try {
            $dsn = "mysql:host=" . self::HOST . ";charset=" . self::CHARSET;
            $pdo = new PDO($dsn, self::USER, self::PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            throw new PDOException("데이터베이스 연결 실패: " . $e->getMessage(), 0, $e);
        }
    }
}
