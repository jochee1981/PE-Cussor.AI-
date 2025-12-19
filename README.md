# PHP 기반 게시판 시스템 (BBS)

XAMPP 기반 PHP 완전한 CRUD 게시판 시스템

## 📋 프로젝트 개요

- **프로젝트명**: XAMPP 기반 PHP 완전한 CRUD 게시판 시스템
- **기술 스택**: PHP 8.0+, MySQL 5.7+, Tailwind CSS, CKEditor 5
- **개발 환경**: XAMPP (Apache + MySQL)

## 🚀 시작하기

### 1. 사전 준비사항

- [x] XAMPP 설치 및 실행
- [x] Apache 서비스 실행 중
- [x] MySQL 서비스 실행 중
- [x] PHP 8.0 이상 버전

### 2. 프로젝트 설정

1. **프로젝트 폴더를 XAMPP의 htdocs에 복사**
   ```
   C:\xampp\htdocs\BBS\
   ```

2. **데이터베이스 생성**
   - 브라우저에서 `http://localhost/BBS/create_database.php` 접속
   - 데이터베이스 생성 완료 메시지 확인

3. **테이블 생성**
   - 브라우저에서 `http://localhost/BBS/setup_database.php` 접속
   - 테이블 생성 완료 메시지 확인

4. **게시판 사용**
   - 브라우저에서 `http://localhost/BBS/index.php` 접속

## 📁 파일 구조

```
BBS/
├── config.php                 # 데이터베이스 연결 설정
├── create_database.php         # DB 자동 생성
├── setup_database.php          # 테이블 자동 생성
├── index.php                   # 게시물 목록
├── write.php                   # 글쓰기
├── view.php                    # 게시물 상세보기
├── edit.php                    # 글 수정
├── delete.php                  # 글 삭제
├── comment.php                 # 댓글 등록 처리
└── README.md                   # 프로젝트 문서
```

## 🎯 주요 기능

### Phase 1: 프로토타입 구축
- ✅ 게시물 목록 표시 (최신순)
- ✅ 게시물 상세보기
- ✅ 글쓰기 페이지

### Phase 2: 에디터 및 DB 설정
- ✅ CKEditor 5 Classic 적용
- ✅ PDO 기반 데이터베이스 연결
- ✅ 자동 데이터베이스/테이블 생성

### Phase 3: CRUD 구현
- ✅ 글 등록 기능
- ✅ 목록 DB 연동
- ✅ 상세보기 + 조회수 증가
- ✅ 댓글 등록/조회

### Bonus 기능
- ✅ 글 수정 기능
- ✅ 글 삭제 기능

## 🔒 보안 기능

- **XSS 방지**: `htmlspecialchars()` 함수로 모든 출력값 이스케이프 처리
- **SQL Injection 방지**: PDO Prepared Statement 사용
- **데이터 유효성 검사**: 필수 필드 검증

## 🗄️ 데이터베이스 구조

### board 테이블
- `id`: 게시물 고유 ID (PK, AUTO_INCREMENT)
- `title`: 제목
- `author`: 작성자
- `content`: 본문 (HTML 포함 가능)
- `created_at`: 작성 일시
- `view_count`: 조회수

### comments 테이블
- `id`: 댓글 고유 ID (PK, AUTO_INCREMENT)
- `board_id`: 게시물 ID (FK)
- `author`: 댓글 작성자
- `content`: 댓글 내용
- `created_at`: 댓글 작성 일시

## 📝 사용 방법

1. **게시물 작성**
   - `index.php`에서 "글쓰기" 버튼 클릭
   - 제목, 작성자, 내용 입력 후 "등록하기" 클릭

2. **게시물 조회**
   - `index.php`에서 제목 클릭
   - 조회수 자동 증가

3. **게시물 수정**
   - 상세보기 페이지에서 "수정" 버튼 클릭
   - 내용 수정 후 "수정하기" 클릭

4. **게시물 삭제**
   - 상세보기 페이지에서 "삭제" 버튼 클릭
   - 확인 후 삭제 (관련 댓글도 자동 삭제)

5. **댓글 작성**
   - 상세보기 페이지 하단에서 댓글 작성
   - 작성자와 내용 입력 후 "댓글 작성" 클릭

## ⚙️ 데이터베이스 설정

기본 설정 (`config.php`):
- **호스트**: localhost
- **DB명**: cursor_db
- **사용자**: root
- **비밀번호**: (없음)
- **문자셋**: utf8mb4

## 🎨 디자인

- **CSS 프레임워크**: Tailwind CSS (CDN)
- **에디터**: CKEditor 5 Classic
- **반응형**: 모바일, 태블릿, 데스크톱 지원

## 📚 참고사항

- XAMPP의 MySQL 기본 포트: 3306
- root 계정 기본 비밀번호: 없음 (공란)
- 접속 URL: `http://localhost/BBS/`

## 🔧 문제 해결

### 데이터베이스 연결 오류
- XAMPP의 MySQL 서비스가 실행 중인지 확인
- `config.php`의 데이터베이스 설정 확인

### 테이블 생성 오류
- `create_database.php`를 먼저 실행했는지 확인
- MySQL 서비스가 정상적으로 실행 중인지 확인

## 🧪 TDD 개발 방법론 - RED 단계 테스트 목록

TDD(Test-Driven Development)의 RED 단계에서는 실패하는 테스트를 먼저 작성합니다. 아래는 작업 보고서를 분석하여 도출한 테스트 케이스 목록입니다.

### 1. 게시물 목록 기능 테스트
- [x] 게시물 목록이 최신순(내림차순)으로 정렬되어 표시되는지 테스트 (`tests/PostListTest.php::testPostsAreSortedByLatestFirst`)
- [x] 게시물 목록에 제목, 작성자, 작성일시, 조회수가 표시되는지 테스트 (`tests/PostListTest.php::testPostListContainsRequiredFields`)
- [x] 게시물이 없을 때 빈 목록이 표시되는지 테스트 (`tests/PostListTest.php::testEmptyPostListIsDisplayedWhenNoPosts`)
- [x] 게시물 목록에서 제목 클릭 시 상세보기 페이지로 이동하는지 테스트 (`tests/PostListTest.php::testPostIdIsAvailableForDetailView`)

**테스트 파일**: `tests/PostListTest.php`

**테스트 실행 방법**:
```bash
# Composer 의존성 설치
composer install

# PHPUnit 테스트 실행
vendor/bin/phpunit tests/PostListTest.php

# 또는 전체 테스트 실행
vendor/bin/phpunit
```

### 2. 게시물 상세보기 기능 테스트
- [x] 게시물 ID로 특정 게시물의 상세 정보를 조회할 수 있는지 테스트 (`tests/PostDetailTest.php::testPostCanBeRetrievedById`)
- [x] 게시물 상세보기 시 조회수가 1 증가하는지 테스트 (`tests/PostDetailTest.php::testViewCountIncreasesWhenViewingPost`)
- [x] 존재하지 않는 게시물 ID로 접근 시 에러 처리가 되는지 테스트 (`tests/PostDetailTest.php::testErrorHandlingForNonExistentPostId`)
- [x] 게시물 상세보기 페이지에 제목, 작성자, 내용, 작성일시, 조회수가 표시되는지 테스트 (`tests/PostDetailTest.php::testPostDetailContainsAllRequiredFields`)

**테스트 파일**: `tests/PostDetailTest.php`

### 3. 게시물 작성 기능 테스트
- [x] 제목, 작성자, 내용이 모두 입력되었을 때 게시물이 정상적으로 등록되는지 테스트 (`tests/PostCreateTest.php::testPostIsCreatedWhenAllFieldsAreProvided`)
- [x] 제목이 비어있을 때 에러 메시지가 표시되는지 테스트 (`tests/PostCreateTest.php::testErrorWhenTitleIsEmpty`)
- [x] 작성자가 비어있을 때 에러 메시지가 표시되는지 테스트 (`tests/PostCreateTest.php::testErrorWhenAuthorIsEmpty`)
- [x] 내용이 비어있을 때 에러 메시지가 표시되는지 테스트 (`tests/PostCreateTest.php::testErrorWhenContentIsEmpty`)
- [ ] 게시물 작성 후 목록 페이지로 리다이렉트되는지 테스트 (통합 테스트 필요)
- [x] 작성된 게시물이 데이터베이스에 저장되는지 테스트 (`tests/PostCreateTest.php::testPostIsSavedToDatabase`)

**테스트 파일**: `tests/PostCreateTest.php`

### 4. 게시물 수정 기능 테스트
- [x] 기존 게시물의 내용이 수정 폼에 정상적으로 로드되는지 테스트 (`tests/PostUpdateTest.php::testExistingPostCanBeLoadedForEditing`)
- [x] 게시물 수정 후 변경사항이 데이터베이스에 반영되는지 테스트 (`tests/PostUpdateTest.php::testPostUpdateReflectsInDatabase`)
- [x] 존재하지 않는 게시물 ID로 수정 시도 시 에러 처리가 되는지 테스트 (`tests/PostUpdateTest.php::testErrorHandlingForNonExistentPostIdOnUpdate`)
- [x] 수정 시 필수 필드(제목, 작성자, 내용) 검증이 동작하는지 테스트 (`tests/PostUpdateTest.php::testRequiredFieldsValidationOnUpdate`)
- [ ] 게시물 수정 후 상세보기 페이지로 리다이렉트되는지 테스트 (통합 테스트 필요)

**테스트 파일**: `tests/PostUpdateTest.php`

### 5. 게시물 삭제 기능 테스트
- [x] 게시물 삭제 시 해당 게시물이 데이터베이스에서 제거되는지 테스트 (`tests/PostDeleteTest.php::testPostIsRemovedFromDatabaseOnDelete`)
- [x] 게시물 삭제 시 관련 댓글도 함께 삭제되는지 테스트 (`tests/PostDeleteTest.php::testCommentsAreDeletedWhenPostIsDeleted`)
- [x] 존재하지 않는 게시물 ID로 삭제 시도 시 에러 처리가 되는지 테스트 (`tests/PostDeleteTest.php::testErrorHandlingForNonExistentPostIdOnDelete`)
- [ ] 게시물 삭제 후 목록 페이지로 리다이렉트되는지 테스트 (통합 테스트 필요)

**테스트 파일**: `tests/PostDeleteTest.php`

### 6. 댓글 기능 테스트
- [x] 댓글이 정상적으로 등록되는지 테스트 (`tests/CommentTest.php::testCommentIsCreatedSuccessfully`)
- [x] 댓글 작성자와 내용이 비어있을 때 에러 메시지가 표시되는지 테스트 (`tests/CommentTest.php::testErrorWhenCommentAuthorOrContentIsEmpty`)
- [x] 특정 게시물의 댓글 목록이 최신순으로 표시되는지 테스트 (`tests/CommentTest.php::testCommentsAreSortedByLatestFirst`)
- [ ] 댓글이 등록된 게시물의 상세보기 페이지에 댓글이 표시되는지 테스트 (통합 테스트 필요)
- [x] 댓글이 데이터베이스에 저장되는지 테스트 (`tests/CommentTest.php::testCommentIsSavedToDatabase`)
- [ ] 댓글 등록 후 페이지가 새로고침되어 댓글이 즉시 표시되는지 테스트 (통합 테스트 필요)

**테스트 파일**: `tests/CommentTest.php`

### 7. 데이터베이스 연결 테스트
- [x] 데이터베이스 연결이 정상적으로 이루어지는지 테스트 (`tests/DatabaseConnectionTest.php::testDatabaseConnectionIsSuccessful`)
- [x] 잘못된 데이터베이스 설정 시 에러 처리가 되는지 테스트 (`tests/DatabaseConnectionTest.php::testErrorHandlingForInvalidDatabaseConfig`)
- [x] PDO Prepared Statement가 정상적으로 동작하는지 테스트 (`tests/DatabaseConnectionTest.php::testPDOPreparedStatementWorks`)

**테스트 파일**: `tests/DatabaseConnectionTest.php`

### 8. 보안 기능 테스트
- [x] XSS 공격 방지: `<script>` 태그가 포함된 입력값이 이스케이프 처리되는지 테스트 (`tests/SecurityTest.php::testXSSPreventionWithScriptTag`)
- [x] SQL Injection 방지: 악의적인 SQL 코드가 실행되지 않는지 테스트 (`tests/SecurityTest.php::testSQLInjectionPrevention`)
- [x] HTML 특수문자(`<`, `>`, `&`, `"`, `'`)가 `htmlspecialchars()`로 처리되는지 테스트 (`tests/SecurityTest.php::testHTMLSpecialCharsEscaping`)
- [ ] POST 요청 외의 다른 HTTP 메서드에 대한 보안 처리가 되는지 테스트 (통합 테스트 필요)

**테스트 파일**: `tests/SecurityTest.php`

### 9. 데이터 유효성 검사 테스트
- [x] 제목 길이 제한 검증 테스트 (`tests/ValidationTest.php::testTitleLengthValidation`)
- [x] 작성자 이름 길이 제한 검증 테스트 (`tests/ValidationTest.php::testAuthorNameLengthValidation`)
- [x] 내용 길이 제한 검증 테스트 (`tests/ValidationTest.php::testContentLengthValidation`)
- [x] 댓글 내용 길이 제한 검증 테스트 (`tests/ValidationTest.php::testCommentContentLengthValidation`)

**테스트 파일**: `tests/ValidationTest.php`

### 10. 에러 처리 테스트
- [x] 데이터베이스 연결 실패 시 적절한 에러 메시지가 표시되는지 테스트 (`tests/ErrorHandlingTest.php::testErrorMessageOnDatabaseConnectionFailure`)
- [x] SQL 쿼리 실행 실패 시 예외 처리가 되는지 테스트 (`tests/ErrorHandlingTest.php::testExceptionHandlingOnSQLQueryFailure`)
- [x] 잘못된 파라미터 전달 시 에러 처리가 되는지 테스트 (`tests/ErrorHandlingTest.php::testErrorHandlingForInvalidParameters`)

**테스트 파일**: `tests/ErrorHandlingTest.php`

---

## 📄 라이선스

교육 목적의 실습 프로젝트

---

**개발 기관**: AI 제조 기술 교육 센터  
**프로젝트 코드**: WB2_Toy-project_PE_Cursor.AI

