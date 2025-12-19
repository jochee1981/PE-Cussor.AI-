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

## 📄 라이선스

교육 목적의 실습 프로젝트

---

**개발 기관**: AI 제조 기술 교육 센터  
**프로젝트 코드**: WB2_Toy-project_PE_Cursor.AI

