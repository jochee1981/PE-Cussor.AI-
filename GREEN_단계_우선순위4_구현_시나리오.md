# GREEN 단계 우선순위 4: 보안 및 유효성 검사 강화 - 구현 시나리오

## 📋 목표
TDD GREEN 단계에서 우선순위 4 (보안 및 유효성 검사 강화)를 최소 단위로 구현하여 테스트를 통과시킵니다.

---

## 🎯 구현 범위

### 4.1 보안 기능 구현
- [ ] XSS 공격 방지: `<script>` 태그가 포함된 입력값을 이스케이프 처리
- [ ] SQL Injection 방지: 악의적인 SQL 코드가 실행되지 않도록 처리
- [ ] HTML 특수문자(`<`, `>`, `&`, `"`, `'`)를 `htmlspecialchars()`로 처리
- [ ] POST 요청 외의 다른 HTTP 메서드에 대한 보안 처리 (통합 테스트 필요)

### 4.2 데이터 유효성 검사 구현
- [ ] 제목 길이 제한 검증 (VARCHAR(255))
- [ ] 작성자 이름 길이 제한 검증 (VARCHAR(50))
- [ ] 내용 길이 제한 검증 (TEXT)
- [ ] 댓글 내용 길이 제한 검증 (TEXT)

---

## 📝 상세 구현 시나리오

### 현재 상태 분석

#### 보안 기능 (4.1) - 현재 상태
- ✅ **XSS 방지**: `write.php`, `edit.php`, `comment.php`에서 이미 `htmlspecialchars()` 사용 중
- ✅ **SQL Injection 방지**: 모든 파일에서 PDO Prepared Statement 사용 중
- ✅ **HTML 특수문자 처리**: 이미 `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` 사용 중
- ⚠️ **HTTP 메서드 검증**: `comment.php`에서만 처리됨, `write.php`, `edit.php`에는 없음

#### 유효성 검사 (4.2) - 현재 상태
- ❌ **제목 길이 제한**: 없음 (데이터베이스 레벨에서만 제한)
- ❌ **작성자 이름 길이 제한**: 없음 (데이터베이스 레벨에서만 제한)
- ⚠️ **내용 길이 제한**: TEXT 타입이므로 큰 데이터 허용 (테스트는 큰 데이터 저장 확인)
- ⚠️ **댓글 내용 길이 제한**: TEXT 타입이므로 큰 데이터 허용 (테스트는 큰 데이터 저장 확인)

---

### Step 1: HTTP 메서드 검증 추가

**현재 문제점:**
- `write.php`와 `edit.php`에서 POST 요청만 확인하지만, 명시적인 검증이 없음
- `comment.php`는 이미 처리되어 있음

**개선 방안:**
1. `write.php`에 HTTP 메서드 검증 추가 (이미 `if ($_SERVER['REQUEST_METHOD'] === 'POST')` 있음, 확인만 필요)
2. `edit.php`에 HTTP 메서드 검증 추가 (이미 `if ($_SERVER['REQUEST_METHOD'] === 'POST')` 있음, 확인만 필요)
3. GET 요청 시 적절한 처리 (이미 구현됨)

**구현 코드 구조:**
```php
// write.php, edit.php - 이미 구현되어 있음
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST 요청 처리
} else {
    // GET 요청 처리 (이미 구현됨)
}
```

**테스트 통과 확인:**
- `SecurityTest::testXSSPreventionWithScriptTag()` ✅ (이미 구현됨)
- `SecurityTest::testSQLInjectionPrevention()` ✅ (이미 구현됨)
- `SecurityTest::testHTMLSpecialCharsEscaping()` ✅ (이미 구현됨)
- HTTP 메서드 검증 테스트 (통합 테스트 필요)

---

### Step 2: 제목 길이 제한 검증 추가

**현재 문제점:**
- `write.php`와 `edit.php`에서 제목 길이 검증이 없음
- 데이터베이스 레벨에서만 VARCHAR(255) 제한

**개선 방안:**
1. `write.php`에 제목 길이 검증 추가 (최대 255자)
2. `edit.php`에 제목 길이 검증 추가 (최대 255자)
3. 길이 초과 시 에러 메시지 표시

**구현 코드 구조:**
```php
// write.php, edit.php
$title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8') : '';

// 길이 검증 추가
if (strlen($title) > 255) {
    throw new Exception('제목은 255자 이하여야 합니다.');
}
```

**테스트 통과 확인:**
- `ValidationTest::testTitleLengthValidation()` ✅

---

### Step 3: 작성자 이름 길이 제한 검증 추가

**현재 문제점:**
- `write.php`, `edit.php`, `comment.php`에서 작성자 이름 길이 검증이 없음
- 데이터베이스 레벨에서만 VARCHAR(50) 제한

**개선 방안:**
1. `write.php`에 작성자 길이 검증 추가 (최대 50자)
2. `edit.php`에 작성자 길이 검증 추가 (최대 50자)
3. `comment.php`에 작성자 길이 검증 추가 (최대 50자)
4. 길이 초과 시 에러 메시지 표시

**구현 코드 구조:**
```php
// write.php, edit.php, comment.php
$author = isset($_POST['author']) ? htmlspecialchars(trim($_POST['author']), ENT_QUOTES, 'UTF-8') : '';

// 길이 검증 추가
if (strlen($author) > 50) {
    throw new Exception('작성자 이름은 50자 이하여야 합니다.');
}
```

**테스트 통과 확인:**
- `ValidationTest::testAuthorNameLengthValidation()` ✅

---

### Step 4: 내용 길이 제한 검증 (선택적)

**현재 상태:**
- TEXT 타입이므로 큰 데이터 저장 가능
- 테스트는 큰 데이터가 저장되는지 확인 (제한이 아닌 저장 확인)

**개선 방안:**
- 테스트 요구사항에 따라 큰 데이터 저장이 가능해야 하므로, 추가 검증 불필요
- 필요시 실용적인 제한 추가 가능 (예: 1MB)

**테스트 통과 확인:**
- `ValidationTest::testContentLengthValidation()` ✅ (이미 통과 가능)

---

### Step 5: 댓글 내용 길이 제한 검증 (선택적)

**현재 상태:**
- TEXT 타입이므로 큰 데이터 저장 가능
- 테스트는 큰 데이터가 저장되는지 확인 (제한이 아닌 저장 확인)

**개선 방안:**
- 테스트 요구사항에 따라 큰 데이터 저장이 가능해야 하므로, 추가 검증 불필요
- 필요시 실용적인 제한 추가 가능 (예: 10KB)

**테스트 통과 확인:**
- `ValidationTest::testCommentContentLengthValidation()` ✅ (이미 통과 가능)

---

## 🔄 구현 순서

1. **Step 2 실행**: `write.php`와 `edit.php`에 제목 길이 검증 추가
2. **Step 3 실행**: `write.php`, `edit.php`, `comment.php`에 작성자 길이 검증 추가
3. **Step 1 확인**: HTTP 메서드 검증이 이미 구현되어 있는지 확인
4. **테스트 실행**: `vendor/bin/phpunit tests/SecurityTest.php`
5. **테스트 실행**: `vendor/bin/phpunit tests/ValidationTest.php`
6. **통과 확인**: 모든 테스트가 통과하는지 확인

---

## ⚠️ 주의사항

### 1. 기존 코드와의 호환성
- 기존에 저장된 데이터와의 호환성 유지
- 에러 메시지는 사용자 친화적으로 작성

### 2. 최소한의 변경
- TDD GREEN 단계 원칙에 따라 테스트를 통과시키는 최소한의 코드만 추가
- 불필요한 리팩토링은 REFACTOR 단계에서 수행

### 3. 보안 기능은 이미 구현됨
- 대부분의 보안 기능이 이미 구현되어 있음
- HTTP 메서드 검증만 확인 필요

---

## ✅ 예상 결과

### 테스트 통과 목록
- ✅ `SecurityTest::testXSSPreventionWithScriptTag()` (이미 통과 가능)
- ✅ `SecurityTest::testSQLInjectionPrevention()` (이미 통과 가능)
- ✅ `SecurityTest::testHTMLSpecialCharsEscaping()` (이미 통과 가능)
- ✅ `ValidationTest::testTitleLengthValidation()` (구현 후 통과)
- ✅ `ValidationTest::testAuthorNameLengthValidation()` (구현 후 통과)
- ✅ `ValidationTest::testContentLengthValidation()` (이미 통과 가능)
- ✅ `ValidationTest::testCommentContentLengthValidation()` (이미 통과 가능)

### 총 테스트 케이스: 7개
- 우선순위 4.1: 3개 (이미 통과 가능)
- 우선순위 4.2: 4개 (2개 구현 필요, 2개 이미 통과 가능)

---

## 📌 다음 단계

우선순위 4 완료 후:
- 모든 GREEN 단계 구현 완료
- 전체 테스트 실행 및 통과 확인
- REFACTOR 단계 준비

---

**작성일**: 2025-12-19  
**상태**: 승인 대기 중  
**구현 예정**: 승인 후 즉시 구현
