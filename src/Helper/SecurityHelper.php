<?php
/**
 * 보안 관련 헬퍼 클래스
 */
class SecurityHelper {
    
    /**
     * XSS 방지를 위한 HTML 이스케이프
     */
    public static function escapeHtml(string $value): string {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * 입력값 정제 (trim + escape)
     */
    public static function sanitizeInput(string $value): string {
        return self::escapeHtml(trim($value));
    }
    
    /**
     * CKEditor HTML 콘텐츠는 그대로 반환 (출력 시 별도 처리 필요)
     */
    public static function sanitizeHtmlContent(string $content): string {
        return trim($content);
    }
}
