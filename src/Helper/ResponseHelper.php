<?php
/**
 * HTTP 응답 관련 헬퍼 클래스
 */
class ResponseHelper {
    
    /**
     * 리다이렉트
     */
    public static function redirect(string $url, ?string $message = null, string $type = 'success'): void {
        if ($message !== null) {
            $param = $type === 'error' ? 'error' : 'success';
            $url .= (strpos($url, '?') !== false ? '&' : '?') . $param . '=' . urlencode($message);
        }
        header("Location: " . $url);
        exit;
    }
    
    /**
     * 에러 응답
     */
    public static function errorResponse(string $message, string $redirectUrl = 'index.php'): void {
        self::redirect($redirectUrl, $message, 'error');
    }
    
    /**
     * 성공 응답
     */
    public static function successResponse(string $message, string $redirectUrl = 'index.php'): void {
        self::redirect($redirectUrl, $message, 'success');
    }
}
