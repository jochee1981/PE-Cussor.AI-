<?php
/**
 * 유효성 검사 결과 클래스
 */
class ValidationResult {
    private bool $isValid;
    private array $errors;
    
    public function __construct(bool $isValid = true, array $errors = []) {
        $this->isValid = $isValid;
        $this->errors = $errors;
    }
    
    public function isValid(): bool {
        return $this->isValid;
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
    
    public function getFirstError(): ?string {
        return $this->errors[0] ?? null;
    }
    
    public function addError(string $error): void {
        $this->isValid = false;
        $this->errors[] = $error;
    }
    
    public static function success(): ValidationResult {
        return new ValidationResult(true);
    }
    
    public static function failure(string $error): ValidationResult {
        return new ValidationResult(false, [$error]);
    }
    
    public static function failureMultiple(array $errors): ValidationResult {
        return new ValidationResult(false, $errors);
    }
}
