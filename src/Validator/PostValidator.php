<?php
require_once __DIR__ . '/ValidationResult.php';
require_once __DIR__ . '/../Entity/Post.php';
require_once __DIR__ . '/../../config/validation.php';

/**
 * 게시물 유효성 검사 클래스
 */
class PostValidator {
    
    public function validateTitle(string $title): ValidationResult {
        if (empty(trim($title))) {
            return ValidationResult::failure('제목을 입력해주세요.');
        }
        
        if (strlen($title) > ValidationRules::TITLE_MAX_LENGTH) {
            return ValidationResult::failure('제목은 ' . ValidationRules::TITLE_MAX_LENGTH . '자 이하여야 합니다.');
        }
        
        return ValidationResult::success();
    }
    
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
            return ValidationResult::failure('내용을 입력해주세요.');
        }
        
        if (strlen($content) > ValidationRules::CONTENT_MAX_LENGTH) {
            return ValidationResult::failure('내용이 너무 깁니다.');
        }
        
        return ValidationResult::success();
    }
    
    public function validate(Post $post): ValidationResult {
        $errors = [];
        
        $titleResult = $this->validateTitle($post->getTitle());
        if (!$titleResult->isValid()) {
            $errors = array_merge($errors, $titleResult->getErrors());
        }
        
        $authorResult = $this->validateAuthor($post->getAuthor());
        if (!$authorResult->isValid()) {
            $errors = array_merge($errors, $authorResult->getErrors());
        }
        
        $contentResult = $this->validateContent($post->getContent());
        if (!$contentResult->isValid()) {
            $errors = array_merge($errors, $contentResult->getErrors());
        }
        
        if (empty($errors)) {
            return ValidationResult::success();
        }
        
        return ValidationResult::failureMultiple($errors);
    }
}
