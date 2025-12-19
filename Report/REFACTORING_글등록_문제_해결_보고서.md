# 글 등록 기능 문제 해결 보고서

## 📋 문제 분석

### 발견된 문제
- 글 등록 기능이 작동하지 않음
- 리팩토링된 새로운 구조와 기존 `write.php` 파일 간 연결 문제

### 원인 분석

#### 1. 구조적 문제
- **이전**: `write.php`가 직접 `config.php`의 `getDBConnection()` 사용
- **현재**: 리팩토링된 구조(Container, PostController) 사용 필요
- **문제**: 기존 파일이 새로운 구조로 마이그레이션되지 않음

#### 2. 데이터 처리 문제
- `SecurityHelper::sanitizeInput()`이 `htmlspecialchars`를 적용하여 데이터베이스 저장 시 문제 발생 가능
- 데이터베이스에는 원본 데이터가 저장되어야 하며, XSS 방지는 출력 시 적용되어야 함

#### 3. 의존성 로드 문제
- `Container`가 모든 의존성을 로드하지만, 순환 참조나 파일 경로 문제 가능성

---

## ✅ 해결 방법

### 1. `write.php` 파일 수정

#### 변경 사항
- 기존 `config.php` 의존성 제거
- `Container`를 통한 의존성 주입 사용
- `PostController`를 통한 요청 처리

#### 수정된 코드 구조
```php
// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = Container::getPostController();
        
        // POST 데이터 받기 (trim만 적용)
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'author' => trim($_POST['author'] ?? ''),
            'content' => trim($_POST['content'] ?? '')
        ];
        
        // Controller를 통해 게시물 등록
        $controller->store($data);
        exit;
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
```

### 2. 데이터 처리 개선

#### 문제점
- `SecurityHelper::sanitizeInput()`은 `htmlspecialchars`를 적용하여 DB 저장 시 문제 발생
- CKEditor HTML 콘텐츠는 원본 그대로 저장되어야 함

#### 해결책
- DB 저장 전: `trim()`만 적용
- 출력 시: `SecurityHelper::escapeHtml()` 적용 (뷰에서 처리)

### 3. 에러 처리 개선

#### 개선 사항
- 예외 발생 시 에러 메시지를 폼에 표시
- 사용자에게 명확한 에러 메시지 제공

---

## 🔍 검증 방법

### 테스트 시나리오

1. **정상 등록 테스트**
   - 제목, 작성자, 내용 모두 입력
   - 예상 결과: 게시물 등록 성공, 목록 페이지로 리다이렉트

2. **유효성 검사 테스트**
   - 제목 비어있음
   - 작성자 비어있음
   - 내용 비어있음
   - 예상 결과: 적절한 에러 메시지 표시

3. **길이 제한 테스트**
   - 제목 255자 초과
   - 작성자 50자 초과
   - 예상 결과: 적절한 에러 메시지 표시

---

## 📝 수정된 파일

### `write.php`
- ✅ `Container` 사용으로 변경
- ✅ `PostController`를 통한 요청 처리
- ✅ 데이터 처리 개선 (trim만 적용)
- ✅ 에러 처리 개선

---

## ⚠️ 주의사항

### 1. 데이터베이스 연결
- `DatabaseConfig::getConnection()`이 정상 작동하는지 확인 필요
- 데이터베이스가 생성되어 있고 테이블이 존재하는지 확인

### 2. 의존성 로드
- `Container.php`가 모든 필요한 파일을 올바르게 require하는지 확인
- 파일 경로가 올바른지 확인

### 3. 유효성 검사
- `PostValidator`가 올바르게 작동하는지 확인
- 에러 메시지가 사용자에게 명확하게 전달되는지 확인

---

## 🎯 다음 단계

1. **다른 파일 마이그레이션**
   - `index.php`: PostController 사용
   - `view.php`: PostController 사용
   - `edit.php`: PostController 사용
   - `delete.php`: PostController 사용
   - `comment.php`: CommentController 사용

2. **테스트 작성**
   - 통합 테스트 작성
   - 에러 케이스 테스트

3. **문서화**
   - 사용 가이드 작성
   - API 문서 작성

---

**작성일**: 2024년 12월 19일  
**상태**: 해결 완료  
**버전**: 1.0
