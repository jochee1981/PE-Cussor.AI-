<?php
require_once __DIR__ . '/ValidationResult.php';
require_once __DIR__ . '/../Entity/Comment.php';
require_once __DIR__ . '/../../config/validation.php';

/**
 * 댓글 유효성 검사 클래스
 */
class CommentValidator {
    
    public function validateAuthor(string $author): ValidationResult {
        if (empty(trim($author))) {
            return ValidationResult::failure('작성자를 입력해주세요.');
        }
        
        if (strlen($author) > ValidationRules::AUTHOR_MAX_LENGTH) {
            return ValidationResult::failure('작성자 이름은 ' . ValidationRules::AUTHOR_MAX_LENGTH . '자 이하여야 합니다.');
        }
        
        return ValidationResult::success();
    }
    
    public function validateContent(string $content): ValidationResult {
        if (empty(trim($content))) {
            return ValidationResult::failure('댓글 내용을 입력해주세요.');
        }
        
        if (strlen($content) > ValidationRules::COMMENT_CONTENT_MAX_LENGTH) {
            return ValidationResult::failure('댓글 내용이 너무 깁니다.');
        }
        
        return ValidationResult::success();
    }
    
    public function validate(Comment $comment): ValidationResult {
        $errors = [];
        
        if ($comment->getBoardId() <= 0) {
            $errors[] = '게시물 ID가 올바르지 않습니다.';
        }
        
        $authorResult = $this->validateAuthor($comment->getAuthor());
        if (!$authorResult->isValid()) {
            $errors = array_merge($errors, $authorResult->getErrors());
        }
        
        $contentResult = $this->validateContent($comment->getContent());
        if (!$contentResult->isValid()) {
            $errors = array_merge($errors, $contentResult->getErrors());
        }
        
        if (empty($errors)) {
            return ValidationResult::success();
        }
        
        return ValidationResult::failureMultiple($errors);
    }
}
