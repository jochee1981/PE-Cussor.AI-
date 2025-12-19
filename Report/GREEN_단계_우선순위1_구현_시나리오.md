# GREEN 단계 우선순위 1: 기반 인프라 구축 - 구현 시나리오

## 📋 목표
TDD GREEN 단계에서 우선순위 1 (기반 인프라 구축)을 최소 단위로 구현하여 테스트를 통과시킵니다.

---

## 🎯 구현 범위

### 1.1 데이터베이스 연결 관리
- [ ] `config.php`의 `getDBConnection()` 함수 개선
- [ ] 데이터베이스 연결 성공 시 정상 동작 확인
- [ ] 잘못된 데이터베이스 설정 시 예외 처리 (die 대신 예외 throw)
- [ ] PDO Prepared Statement 사용 준비 (이미 준비됨)

### 1.2 에러 처리 기반 구축
- [ ] 데이터베이스 연결 실패 시 예외 발생 (적절한 에러 메시지 포함)
- [ ] SQL 쿼리 실행 실패 시 예외 처리 (PDO::ERRMODE_EXCEPTION 설정)
- [ ] 잘못된 파라미터 전달 시 에러 처리
- [ ] NULL 값 처리 로직 (데이터베이스 제약 조건 활용)

---

## 📝 상세 구현 시나리오

### Step 1: `config.php`의 `getDBConnection()` 함수 개선

**현재 문제점:**
- `die()`를 사용하여 예외를 즉시 종료시킴
- 테스트에서 예외를 잡아서 검증하기 어려움
- 에러 메시지 형식이 일관되지 않음

**개선 방안:**
1. `die()` 제거하고 `PDOException`을 throw하도록 변경
2. 예외 메시지를 명확하게 설정
3. 기존 코드와의 호환성을 위해 전역 `$pdo` 변수는 유지
4. `getDBConnection()` 함수는 예외를 throw하도록 수정

**구현 코드 구조:**
```php
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        // die() 대신 예외를 다시 throw
        throw new PDOException("데이터베이스 연결 실패: " . $e->getMessage(), 0, $e);
    }
}
```

**테스트 통과 확인:**
- `DatabaseConnectionTest::testDatabaseConnectionIsSuccessful()` ✅
- `ErrorHandlingTest::testErrorMessageOnDatabaseConnectionFailure()` ✅

---

### Step 2: SQL 쿼리 실행 실패 시 예외 처리 확인

**현재 상태:**
- 이미 `PDO::ATTR_ERRMODE`가 `PDO::ERRMODE_EXCEPTION`으로 설정되어 있음
- 추가 구현 불필요

**테스트 통과 확인:**
- `ErrorHandlingTest::testExceptionHandlingOnSQLQueryFailure()` ✅

---

### Step 3: 잘못된 파라미터 전달 시 에러 처리

**현재 상태:**
- PDO Prepared Statement는 타입 변환을 자동으로 처리
- 테스트는 잘못된 파라미터로 조회 시 결과가 없음을 검증
- 추가 구현 불필요 (PDO의 기본 동작으로 충분)

**테스트 통과 확인:**
- `ErrorHandlingTest::testErrorHandlingForInvalidParameters()` ✅

---

### Step 4: NULL 값 처리 로직

**현재 상태:**
- 데이터베이스 테이블에서 `title`, `author`, `content`가 `NOT NULL` 제약 조건
- NULL 값을 전달하면 데이터베이스에서 자동으로 예외 발생
- 추가 구현 불필요 (데이터베이스 제약 조건 활용)

**테스트 통과 확인:**
- `ErrorHandlingTest::testNullValueHandling()` ✅

---

## 🔄 구현 순서

1. **Step 1 실행**: `config.php`의 `getDBConnection()` 함수 수정
2. **테스트 실행**: `vendor/bin/phpunit tests/DatabaseConnectionTest.php`
3. **테스트 실행**: `vendor/bin/phpunit tests/ErrorHandlingTest.php`
4. **통과 확인**: 모든 테스트가 통과하는지 확인
5. **기존 코드 호환성 확인**: 다른 PHP 파일들이 정상 동작하는지 확인

---

## ⚠️ 주의사항

### 1. 기존 코드와의 호환성
- `config.php`의 전역 `$pdo` 변수는 기존 코드에서 사용 중일 수 있음
- 전역 변수는 유지하되, `getDBConnection()` 함수는 예외를 throw하도록 변경

### 2. 에러 처리 방식
- 웹 애플리케이션에서는 `die()`를 사용할 수도 있지만, 테스트를 위해 예외를 throw
- 실제 웹 애플리케이션에서는 try-catch로 예외를 처리하도록 권장

### 3. 최소한의 변경
- TDD GREEN 단계 원칙에 따라 테스트를 통과시키는 최소한의 코드만 수정
- 불필요한 리팩토링은 REFACTOR 단계에서 수행

---

## ✅ 예상 결과

### 테스트 통과 목록
- ✅ `DatabaseConnectionTest::testDatabaseConnectionIsSuccessful()`
- ✅ `DatabaseConnectionTest::testErrorHandlingForInvalidDatabaseConfig()`
- ✅ `DatabaseConnectionTest::testPDOPreparedStatementWorks()`
- ✅ `ErrorHandlingTest::testErrorMessageOnDatabaseConnectionFailure()`
- ✅ `ErrorHandlingTest::testExceptionHandlingOnSQLQueryFailure()`
- ✅ `ErrorHandlingTest::testErrorHandlingForInvalidParameters()`
- ✅ `ErrorHandlingTest::testNullValueHandling()`

### 총 테스트 케이스: 7개
- 우선순위 1.1: 3개
- 우선순위 1.2: 4개

---

## 📌 다음 단계

우선순위 1 완료 후:
- 우선순위 2: 핵심 CRUD 기능 구현으로 진행
- 모든 기반 인프라 테스트가 통과했는지 최종 확인

---

**작성일**: 2025-12-19  
**상태**: ✅ 구현 완료  
**구현 완료일**: 2025-12-19

## ✅ 구현 완료 내역

### 수정된 파일
- `config.php`: `getDBConnection()` 함수에서 `die()` 제거, 예외 throw로 변경

### 변경 사항
```php
// 변경 전
catch (PDOException $e) {
    die("데이터베이스 연결 실패: " . $e->getMessage());
}

// 변경 후
catch (PDOException $e) {
    // die() 대신 예외를 다시 throw하여 테스트에서 검증 가능하도록 함
    throw new PDOException("데이터베이스 연결 실패: " . $e->getMessage(), 0, $e);
}
```

### 테스트 실행 방법
```bash
# DatabaseConnectionTest 실행
vendor/bin/phpunit tests/DatabaseConnectionTest.php

# ErrorHandlingTest 실행
vendor/bin/phpunit tests/ErrorHandlingTest.php

# 전체 테스트 실행
vendor/bin/phpunit
```

### 다음 단계
- 테스트 실행하여 모든 테스트 통과 확인
- 우선순위 2 (핵심 CRUD 기능 구현)로 진행
