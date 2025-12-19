# PRD: PHP 기반 게시판 시스템 (BBS)
**문서 버전**: 1.0  
**작성일**: 2025-01-28  
**프로젝트 코드**: WB2_Toy-project_PE_Cursor.AI  
**개발 기관**: AI 제조 기술 교육 센터

---

## 1. 프로젝트 개요

### 1.1 프로젝트명
**XAMPP 기반 PHP 완전한 CRUD 게시판 시스템**

### 1.2 프로젝트 목표
- PHP와 MySQL을 활용한 웹 개발 기초 습득
- Cursor.AI를 활용한 효율적인 개발 프로세스 학습
- 실무 수준의 게시판 시스템 구현 (보안, 데이터베이스 설계 포함)
- Tailwind CSS를 이용한 모던 UI 설계 역량 강화

### 1.3 프로젝트 기간
**2025-11-01 ~ 2025-12-15** (예상 6주)

### 1.4 프로젝트 성격
- 교육 목적의 실습 프로젝트
- 로컬 개발 환경(XAMPP)에서 진행
- 단계별 구현으로 점진적 학습

---

## 2. 기술 스택

| 계층 | 기술 | 버전 | 용도 |
|------|------|------|------|
| **Backend** | PHP | 8.0+ | 서버 로직 처리 |
| **Database** | MySQL | 5.7+ | 데이터 저장 |
| **Frontend** | HTML5 | - | 마크업 |
| **Styling** | Tailwind CSS | CDN | UI/UX 디자인 |
| **Rich Editor** | CKEditor 5 | Classic (CDN) | 본문 에디터 |
| **API 패턴** | PDO | - | 데이터베이스 보안 연결 |
| **IDE** | Cursor.AI | 최신 버전 | 개발 도구 |
| **Local Server** | XAMPP | - | Apache + MySQL 실행 |

---

## 3. 핵심 요구사항

### 3.1 기능 요구사항 (총 12개 단계)

#### Phase 1: 프로젝트 초기화 (Step 1-4)
| Step | 기능 | 설명 |
|------|------|------|
| 1 | 프로젝트 초기화 | index.php 생성, Tailwind CDN 적용, 하드코딩 샘플 데이터 3개 표시 |
| 2 | 정렬 수정 | 게시물 목록을 최신글 우선(내림차순)으로 변경 |
| 3 | 상세보기 페이지 | view.php 생성, 게시물 상세 내용 + 댓글 영역 |
| 4 | 글쓰기 페이지 | write.php 생성, 제목/작성자/내용 입력 폼 |

#### Phase 2: 에디터 및 DB 설정 (Step 5-8)
| Step | 기능 | 설명 |
|------|------|------|
| 5 | CKEditor 적용 | write.php의 textarea를 CKEditor 5 Classic으로 교체 |
| 6 | DB 연결 설정 | config.php 생성, PDO 기반 MySQL 연결 |
| 7 | 데이터베이스 생성 | create_database.php로 cursor_db 자동 생성 |
| 8 | 테이블 생성 | setup_database.php로 board, comments 테이블 자동 생성 |

#### Phase 3: CRUD 구현 (Step 9-12)
| Step | 기능 | 설명 |
|------|------|------|
| 9 | 글 등록 기능 | write.php에서 POST 데이터를 board 테이블에 INSERT |
| 10 | 목록 조회 | index.php에서 DB 데이터 동적 불러오기 |
| 11 | 상세보기 + 조회수 | view.php에서 게시물 조회 시 view_count 증가 |
| 12 | 댓글 기능 | comments 테이블 및 comment.php로 댓글 등록/조회 |

#### Bonus: 추가 기능
| 기능 | 설명 |
|------|------|
| 글 수정 (edit.php) | 기존 내용을 폼에 불러와 UPDATE 처리 |
| 글 삭제 (delete.php) | DELETE 쿼리로 해당 게시물 삭제 |

### 3.2 보안 요구사항
- **XSS 방지**: `htmlspecialchars()` 함수로 모든 출력값 이스케이프 처리
- **SQL Injection 방지**: PDO Prepared Statement 사용 (바인딩 파라미터)
- **데이터베이스 연결**: config.php를 통한 중앙 집중식 연결 관리
- **에러 처리**: 예외 발생 시 적절한 에러 메시지 표시

### 3.3 디자인 요구사항
- **Design System**: Tailwind CSS 코어 유틸리티 클래스 사용
- **일관성**: 모든 페이지가 동일한 Tailwind 스타일 적용
- **반응형**: 모바일, 태블릿, 데스크톱에서 적절한 레이아웃
- **가독성**: 명확한 타이포그래피와 충분한 여백

---

## 4. 데이터베이스 설계

### 4.1 board 테이블
```
CREATE TABLE board (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  author VARCHAR(50) NOT NULL,
  content LONGTEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  view_count INT DEFAULT 0
);
```

| 컬럼명 | 타입 | 제약조건 | 설명 |
|--------|------|---------|------|
| id | INT | PK, AUTO_INCREMENT | 게시물 고유 ID |
| title | VARCHAR(255) | NOT NULL | 게시물 제목 |
| author | VARCHAR(50) | NOT NULL | 작성자명 |
| content | LONGTEXT | NOT NULL | 게시물 본문 (HTML 포함 가능) |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | 작성 일시 |
| view_count | INT | DEFAULT 0 | 조회수 |

### 4.2 comments 테이블
```
CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  board_id INT NOT NULL,
  author VARCHAR(50) NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (board_id) REFERENCES board(id) ON DELETE CASCADE
);
```

| 컬럼명 | 타입 | 제약조건 | 설명 |
|--------|------|---------|------|
| id | INT | PK, AUTO_INCREMENT | 댓글 고유 ID |
| board_id | INT | FK | 게시물 ID (참조키) |
| author | VARCHAR(50) | NOT NULL | 댓글 작성자명 |
| content | TEXT | NOT NULL | 댓글 내용 |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | 댓글 작성 일시 |

### 4.3 데이터베이스 설정
- **DB명**: cursor_db
- **호스트**: localhost
- **포트**: 3306
- **사용자**: root
- **비밀번호**: (없음, 빈 문자열)
- **문자셋**: utf8mb4
- **콜레이션**: utf8mb4_unicode_ci

---

## 5. 페이지 구조 및 흐름도

### 5.1 사용자 흐름도
```
index.php (목록)
    ↓
    ├─→ "글쓰기" 버튼 → write.php (작성)
    │                      ↓
    │                    등록 → index.php로 이동
    │
    └─→ 제목 클릭 → view.php (상세보기)
                      ↓
                    ├─ "수정" → edit.php → UPDATE → view.php
                    ├─ "삭제" → delete.php → DELETE → index.php
                    └─ "댓글 작성" → comment.php → INSERT → view.php로 이동
```

### 5.2 파일 구조
```
C:\DEV\Cursor_pro\BBS\
├── config.php                 (데이터베이스 연결 설정)
├── create_database.php         (DB 자동 생성)
├── setup_database.php          (테이블 자동 생성)
├── index.php                   (게시물 목록)
├── write.php                   (글쓰기)
├── view.php                    (게시물 상세보기)
├── edit.php                    (글 수정)
├── delete.php                  (글 삭제)
└── comment.php                 (댓글 등록 처리)
```

---

## 6. 상세 페이지 명세

### 6.1 index.php (게시물 목록)
**목적**: 모든 게시물을 최신순으로 표시
- 상단: "글쓰기" 버튼
- 테이블: 번호, 제목, 작성자, 작성일, 조회수
- 제목은 `view.php?id=n` 링크
- 데이터: PDO로 `SELECT * FROM board ORDER BY id DESC`
- 디자인: Tailwind CSS 모던 테이블

### 6.2 write.php (글쓰기)
**목적**: 새 게시물 작성
- 제목 입력창 (text input)
- 작성자 입력창 (text input)
- 본문 에디터 (CKEditor 5 Classic)
- "등록하기" 버튼 → POST로 처리
- "취소" 버튼 → index.php로 이동
- POST 처리 시 htmlspecialchars + PDO 사용
- 성공 후: `header("Location: index.php");`

### 6.3 view.php (게시물 상세보기)
**목적**: 게시물 전문 표시 및 댓글 관리
- GET 파라미터로 `id` 받음
- id 없으면 "게시물이 없습니다" 출력
- 표시 내용: 제목, 작성자, 작성일, 조회수, 본문
- 조회수: 페이지 로드 시 `UPDATE board SET view_count = view_count + 1`
- 하단 버튼: "목록으로", "수정", "삭제"
- 댓글 목록: `SELECT * FROM comments WHERE board_id = ? ORDER BY created_at DESC`
- 댓글 작성 폼: POST로 comment.php 전송

### 6.4 edit.php (글 수정)
**목적**: 기존 게시물 수정
- GET 파라미터로 `id` 받음
- 해당 id의 글 SELECT으로 폼에 미리 채우기
- write.php와 동일한 폼 구조
- "수정하기" 버튼 클릭 시 `UPDATE board` 처리
- 성공 후: `header("Location: view.php?id=$id");`

### 6.5 delete.php (글 삭제)
**목적**: 게시물 삭제
- GET 파라미터로 `id` 받음
- `DELETE FROM board WHERE id = ?` 실행
- 관련 댓글도 자동 삭제 (FOREIGN KEY CASCADE)
- 성공 후: `header("Location: index.php");`

### 6.6 comment.php (댓글 처리)
**목적**: 댓글 등록
- POST로 board_id, author, content 받음
- `INSERT INTO comments (board_id, author, content) VALUES (?, ?, ?)`
- 성공 후: `header("Location: view.php?id=$board_id");`

### 6.7 config.php (데이터베이스 연결)
**목적**: 중앙 집중식 DB 연결 관리
```php
- PDO 드라이버: mysql
- 호스트: localhost
- DB명: cursor_db
- 문자셋: utf8mb4
- 예외 처리: setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)
```

### 6.8 create_database.php (DB 생성)
**목적**: cursor_db 자동 생성
- config.php include
- `CREATE DATABASE cursor_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`
- 성공: "데이터베이스 생성 완료!" 출력
- 실패: 에러 메시지 출력

### 6.9 setup_database.php (테이블 생성)
**목적**: board, comments 테이블 자동 생성
- config.php include
- board 테이블 생성
- comments 테이블 생성
- 이미 존재: "이미 테이블이 있습니다" 출력
- 성공: "테이블 생성 완료! 이제 http://localhost/BBS 로 접속하세요" 출력

---

## 7. 개발 로드맵

### Phase 1: 프로토타입 구축 (Week 1-2)
| Step | 작업 | 예상 시간 | 담당 |
|------|------|---------|------|
| 1 | 프로젝트 초기화 + index.php | 30분 | 개발자 |
| 2 | 정렬 수정 | 10분 | 개발자 |
| 3 | view.php 생성 | 30분 | 개발자 |
| 4 | write.php 생성 | 30분 | 개발자 |
| **소계** | **UI 프로토타입 완성** | **100분** | |

### Phase 2: 인프라 구축 (Week 2-3)
| Step | 작업 | 예상 시간 | 담당 |
|------|------|---------|------|
| 5 | CKEditor 적용 | 20분 | 개발자 |
| 6 | config.php 작성 | 30분 | 개발자 |
| 7 | create_database.php | 20분 | 개발자 |
| 8 | setup_database.php | 30분 | 개발자 |
| **소계** | **DB 및 설정 완성** | **100분** | |

### Phase 3: 기능 구현 (Week 3-5)
| Step | 작업 | 예상 시간 | 담당 |
|------|------|---------|------|
| 9 | 글 등록 기능 완성 | 30분 | 개발자 |
| 10 | 목록 DB 연동 | 30분 | 개발자 |
| 11 | 상세보기 + 조회수 | 40분 | 개발자 |
| 12 | 댓글 기능 | 50분 | 개발자 |
| **소계** | **CRUD 기능 완성** | **150분** | |

### Phase 4: 보너스 및 최적화 (Week 5-6)
| 작업 | 예상 시간 | 담당 |
|------|---------|------|
| edit.php (수정 기능) | 40분 | 개발자 |
| delete.php (삭제 기능) | 20분 | 개발자 |
| 보안 검토 및 최적화 | 30분 | 개발자 |
| 테스트 및 버그 수정 | 40분 | 개발자 |
| **소계** | **130분** | |

---

## 8. 사전 준비사항

### 8.1 설치 요구사항
- [ ] Cursor.AI 최신 버전 설치
- [ ] XAMPP 설치 (Apache + MySQL 포함)
- [ ] Apache + MySQL 서비스 실행 중
- [ ] PHP 8.0 이상 버전

### 8.2 프로젝트 초기 설정
- [ ] 폴더 생성: `C:\DEV\Cursor_pro\BBS`
- [ ] Cursor에서 해당 폴더 열기 (File → Open Folder)
- [ ] git init으로 버전 관리 시작 (선택사항)

### 8.3 Cursor.AI 활용 방법
- Composer (Ctrl+L)에 제공된 단계별 프롬프트 복사-붙여넣기
- 또는 최하단의 "한 방에 모든 파일 생성 프롬프트" 사용 (10초 완성)

---

## 9. 성공 기준

### 9.1 기능 완성도
- [ ] 모든 12단계 기능 구현 완료
- [ ] 보너스 기능 (edit, delete) 추가 구현
- [ ] 데이터베이스 자동 생성 스크립트 정상 작동

### 9.2 보안 및 품질
- [ ] XSS 방지 (htmlspecialchars 적용)
- [ ] SQL Injection 방지 (PDO Prepared Statement 사용)
- [ ] 에러 처리 및 유효성 검사 완료
- [ ] 문자셋 utf8mb4로 정상 한글 처리

### 9.3 UI/UX
- [ ] Tailwind CSS로 모던하고 일관된 디자인
- [ ] 모든 버튼과 링크 정상 작동
- [ ] 반응형 레이아웃 적용
- [ ] CKEditor로 풍부한 본문 편집 기능

### 9.4 학습 목표
- [ ] PHP 기초 문법 숙지
- [ ] MySQL 및 PDO 이해
- [ ] Cursor.AI 활용 능력 향상
- [ ] 웹 보안 기초 지식 습득

---

## 10. 참고사항

### 10.1 Cursor.AI 활용 팁
```
만능 프롬프트 (가장 빠른 방법):
C:\DEV\Cursor_pro\BBS 폴더에
XAMPP + PHP 8 + MySQL + Tailwind CSS + CKEditor 5를 사용한
완전한 CRUD 게시판을 만들어 주세요.
(이하 생략)

→ 이 프롬프트를 Cursor Composer(Ctrl+L)에 붙여넣으면 10초 안에 완성!
```

### 10.2 XAMPP 기본 설정
- MySQL 기본 포트: 3306
- root 계정 기본 비밀번호: 없음 (공란)
- 접속 URL: http://localhost/BBS/

### 10.3 CKEditor 5 CDN
```html
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
```

### 10.4 Tailwind CSS CDN
```html
<script src="https://cdn.tailwindcss.com"></script>
```

---

## 11. 변경 이력

| 버전 | 작성일 | 변경사항 |
|------|--------|---------|
| 1.0 | 2025-01-28 | 초판 작성 |
| 1.1 | - | (예정) |
| 1.2 | - | (예정) |

---

**문서 작성자**: AI 제조 기술 교육 센터  
**최종 검토일**: 2025-01-28  
**승인자**: -