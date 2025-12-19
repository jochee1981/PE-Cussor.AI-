# 리팩토링 계획서

## 📋 개요

현재 BBS 프로젝트의 코드를 분석하여 코드 스멜, 정적 분석, SOLID 원칙을 고려한 단계별 리팩토링 계획을 수립합니다.

---

## 🔍 1단계: 코드 스멜 분석

### 1.1 발견된 코드 스멜

#### 🔴 Critical (심각)
1. **코드 중복 (Duplicated Code)**
   - 유효성 검사 로직이 `write.php`, `edit.php`, `comment.php`에 중복
   - XSS 방지 처리 (`htmlspecialchars`)가 여러 곳에 반복
   - DB 연결 코드가 모든 파일에 `require_once 'config.php'`로 포함

2. **긴 메서드/파일 (Long Method/File)**
   - 각 PHP 파일이 비즈니스 로직, DB 접근, 뷰 렌더링을 모두 포함
   - `view.php`: 139줄, `write.php`: 113줄, `edit.php`: 138줄

3. **매직 넘버/문자열 (Magic Numbers/Strings)**
   - 하드코딩된 길이 제한: `255`, `50`
   - 하드코딩된 테이블명: `'board'`, `'comments'`
   - 하드코딩된 필드명: `'title'`, `'author'`, `'content'`

#### 🟠 Major (중요)
4. **데이터 클래스 부족 (Data Class)**
   - 배열 기반 데이터 처리로 타입 안정성 부족
   - `$post`, `$comments` 등이 연관 배열로만 처리됨

5. **기본 타입 집착 (Primitive Obsession)**
   - ID, 제목, 작성자 등이 단순 문자열/정수로 처리
   - 도메인 모델 부재

6. **긴 파라미터 리스트 (Long Parameter List)**
   - 유효성 검사 함수가 여러 파라미터를 받아야 함

#### 🟡 Minor (경미)
7. **주석으로 설명하는 코드 (Comments)**
   - 일부 주석이 코드의 의도를 설명하는 대신 동작을 설명
   - `// CKEditor는 HTML이므로 htmlspecialchars는 나중에 출력 시 적용`

8. **죽은 코드 (Dead Code)**
   - `config.php`에 사용되지 않는 전역 `$pdo` 변수

---

## 🔬 2단계: 정적 분석

### 2.1 아키텍처 문제

1. **관심사의 분리 부족 (Separation of Concerns)**
   ```
   현재 구조:
   index.php, write.php, view.php, edit.php, delete.php, comment.php
   ├── 비즈니스 로직 (유효성 검사, 데이터 처리)
   ├── 데이터베이스 접근 (PDO 쿼리)
   └── 뷰 렌더링 (HTML 출력)
   ```

2. **레이어드 아키텍처 부재**
   - Presentation Layer (뷰)
   - Business Logic Layer (서비스)
   - Data Access Layer (리포지토리)
   - Domain Layer (엔티티)

### 2.2 의존성 문제

1. **강한 결합 (Tight Coupling)**
   - 모든 파일이 `config.php`에 직접 의존
   - DB 연결이 전역 함수로 처리됨

2. **의존성 주입 부재**
   - 객체 간 의존성이 하드코딩됨
   - 테스트 어려움

### 2.3 에러 처리 일관성 부족

1. **혼재된 에러 처리 방식**
   - 일부: `try-catch` + 예외 throw
   - 일부: `die()` 사용
   - 일부: 리다이렉트로 에러 전달

---

## 🏗️ 3단계: SOLID 원칙 위반 분석

### 3.1 Single Responsibility Principle (SRP) 위반

**문제점:**
- `write.php`: 게시물 작성 로직 + 유효성 검사 + DB 접근 + HTML 렌더링
- `view.php`: 게시물 조회 + 조회수 증가 + 댓글 조회 + HTML 렌더링
- `edit.php`: 게시물 수정 로직 + 유효성 검사 + DB 접근 + HTML 렌더링

**책임 분리 필요:**
- Controller: HTTP 요청 처리
- Service: 비즈니스 로직
- Repository: 데이터 접근
- View: 렌더링

### 3.2 Open/Closed Principle (OCP) 위반

**문제점:**
- 새로운 유효성 검사 규칙 추가 시 여러 파일 수정 필요
- 새로운 필드 추가 시 모든 CRUD 파일 수정 필요

**해결책:**
- 전략 패턴으로 유효성 검사 규칙 확장 가능하게
- 엔티티 기반으로 필드 관리

### 3.3 Liskov Substitution Principle (LSP)

**현재 상태:**
- 상속 구조가 없어 직접적인 위반은 없음
- 하지만 인터페이스 부재로 확장성 제한

### 3.4 Interface Segregation Principle (ISP)

**문제점:**
- 모든 기능이 하나의 파일에 집중
- 인터페이스 부재로 의존성 역전 불가

### 3.5 Dependency Inversion Principle (DIP) 위반

**문제점:**
- 고수준 모듈이 저수준 모듈(PDO)에 직접 의존
- `getDBConnection()` 전역 함수 사용

**해결책:**
- Repository 인터페이스 도입
- 의존성 주입 컨테이너 사용

---

## 📐 4단계: 리팩토링 전략

### 4.1 리팩토링 우선순위

#### Phase 1: 기반 구조 개선 (최우선)
1. **디렉토리 구조 재구성**
   ```
   BBS/
   ├── src/
   │   ├── Controller/      # 컨트롤러
   │   ├── Service/          # 비즈니스 로직
   │   ├── Repository/       # 데이터 접근
   │   ├── Entity/           # 도메인 모델
   │   ├── Validator/        # 유효성 검사
   │   └── Exception/        # 커스텀 예외
   ├── views/                # 뷰 템플릿
   ├── config/               # 설정 파일
   └── public/               # 공개 접근 파일
   ```

2. **의존성 주입 기반 구조**
   - Repository 인터페이스 정의
   - Service 클래스 생성
   - Controller 클래스 생성

#### Phase 2: 코드 중복 제거
1. **유효성 검사 클래스 생성**
   - `PostValidator` 클래스
   - `CommentValidator` 클래스
   - 공통 유효성 검사 규칙 추출

2. **공통 유틸리티 클래스**
   - `SecurityHelper`: XSS 방지, 입력 정제
   - `ResponseHelper`: 리다이렉트, 에러 응답

#### Phase 3: 도메인 모델 도입
1. **엔티티 클래스 생성**
   - `Post` 엔티티
   - `Comment` 엔티티
   - 타입 안정성 확보

2. **Repository 패턴 구현**
   - `PostRepository` 인터페이스 및 구현
   - `CommentRepository` 인터페이스 및 구현

#### Phase 4: 서비스 레이어 분리
1. **Service 클래스 생성**
   - `PostService`: 게시물 비즈니스 로직
   - `CommentService`: 댓글 비즈니스 로직

2. **Controller 클래스 생성**
   - `PostController`: HTTP 요청 처리
   - `CommentController`: 댓글 요청 처리

#### Phase 5: 뷰 분리
1. **템플릿 시스템 도입**
   - 공통 레이아웃 분리
   - 부분 뷰(Partial View) 생성
   - 뷰 헬퍼 함수

---

## 🛠️ 5단계: 구체적 리팩토링 작업

### 5.1 Phase 1: 기반 구조 개선

#### 작업 1.1: 디렉토리 구조 생성
```php
// 새 디렉토리 구조
src/
├── Entity/
│   ├── Post.php
│   └── Comment.php
├── Repository/
│   ├── PostRepositoryInterface.php
│   ├── PostRepository.php
│   ├── CommentRepositoryInterface.php
│   └── CommentRepository.php
├── Service/
│   ├── PostService.php
│   └── CommentService.php
├── Controller/
│   ├── PostController.php
│   └── CommentController.php
├── Validator/
│   ├── PostValidator.php
│   └── CommentValidator.php
└── Helper/
    ├── SecurityHelper.php
    └── ResponseHelper.php
```

#### 작업 1.2: 설정 파일 개선
```php
// config/database.php
class DatabaseConfig {
    const HOST = 'localhost';
    const NAME = 'cursor_db';
    const USER = 'root';
    const PASS = '';
    const CHARSET = 'utf8mb4';
}

// config/validation.php
class ValidationRules {
    const TITLE_MAX_LENGTH = 255;
    const AUTHOR_MAX_LENGTH = 50;
    const CONTENT_MAX_LENGTH = 65535; // TEXT 타입
}
```

### 5.2 Phase 2: 엔티티 및 Repository 패턴

#### 작업 2.1: Post 엔티티 생성
```php
// src/Entity/Post.php
class Post {
    private ?int $id;
    private string $title;
    private string $author;
    private string $content;
    private DateTime $createdAt;
    private int $viewCount;
    
    // Getters, Setters, 생성자
}
```

#### 작업 2.2: PostRepository 인터페이스 및 구현
```php
// src/Repository/PostRepositoryInterface.php
interface PostRepositoryInterface {
    public function findAll(): array;
    public function findById(int $id): ?Post;
    public function save(Post $post): Post;
    public function update(Post $post): bool;
    public function delete(int $id): bool;
    public function incrementViewCount(int $id): bool;
}

// src/Repository/PostRepository.php
class PostRepository implements PostRepositoryInterface {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    // 구현...
}
```

### 5.3 Phase 3: 유효성 검사 클래스

#### 작업 3.1: PostValidator 생성
```php
// src/Validator/PostValidator.php
class PostValidator {
    public function validateTitle(string $title): ValidationResult;
    public function validateAuthor(string $author): ValidationResult;
    public function validateContent(string $content): ValidationResult;
    public function validate(Post $post): ValidationResult;
}
```

### 5.4 Phase 4: Service 레이어

#### 작업 4.1: PostService 생성
```php
// src/Service/PostService.php
class PostService {
    private PostRepositoryInterface $postRepository;
    private PostValidator $validator;
    
    public function __construct(
        PostRepositoryInterface $postRepository,
        PostValidator $validator
    ) {
        $this->postRepository = $postRepository;
        $this->validator = $validator;
    }
    
    public function createPost(array $data): Post;
    public function updatePost(int $id, array $data): Post;
    public function deletePost(int $id): bool;
    public function getPost(int $id): Post;
    public function getAllPosts(): array;
}
```

### 5.5 Phase 5: Controller 레이어

#### 작업 5.1: PostController 생성
```php
// src/Controller/PostController.php
class PostController {
    private PostService $postService;
    
    public function __construct(PostService $postService) {
        $this->postService = $postService;
    }
    
    public function index(): void;
    public function show(int $id): void;
    public function create(): void;
    public function store(array $data): void;
    public function edit(int $id): void;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
}
```

### 5.6 Phase 6: 뷰 분리

#### 작업 6.1: 템플릿 시스템
```php
// views/layout.php (공통 레이아웃)
// views/post/index.php (목록)
// views/post/show.php (상세보기)
// views/post/create.php (작성)
// views/post/edit.php (수정)
```

---

## 📊 6단계: 리팩토링 체크리스트

### Phase 1: 기반 구조
- [ ] 디렉토리 구조 생성
- [ ] 설정 파일 클래스화
- [ ] 의존성 주입 준비

### Phase 2: 엔티티 및 Repository
- [ ] Post 엔티티 생성
- [ ] Comment 엔티티 생성
- [ ] Repository 인터페이스 정의
- [ ] Repository 구현체 생성

### Phase 3: 유효성 검사
- [ ] PostValidator 클래스
- [ ] CommentValidator 클래스
- [ ] ValidationResult 클래스

### Phase 4: Service 레이어
- [ ] PostService 클래스
- [ ] CommentService 클래스
- [ ] 의존성 주입 설정

### Phase 5: Controller 레이어
- [ ] PostController 클래스
- [ ] CommentController 클래스
- [ ] 라우팅 시스템

### Phase 6: 뷰 분리
- [ ] 레이아웃 템플릿
- [ ] 부분 뷰 생성
- [ ] 뷰 헬퍼 함수

### Phase 7: 테스트 및 검증
- [ ] 기존 테스트 통과 확인
- [ ] 새로운 구조에 맞는 테스트 수정
- [ ] 통합 테스트 작성

---

## 🎯 7단계: 리팩토링 원칙

### 7.1 점진적 리팩토링
- 한 번에 하나의 레이어씩 리팩토링
- 각 단계마다 테스트 실행하여 회귀 방지
- 기존 기능 유지 보장

### 7.2 테스트 우선
- 리팩토링 전 기존 테스트 모두 통과 확인
- 리팩토링 후 동일한 테스트 통과 확인
- 새로운 테스트 추가로 리팩토링 검증

### 7.3 작은 단위로 진행
- 큰 변경을 작은 커밋으로 분할
- 각 커밋은 독립적으로 테스트 가능
- 롤백 가능한 단위로 진행

---

## 📈 8단계: 예상 효과

### 8.1 코드 품질 개선
- **코드 중복 제거**: 30-40% 감소 예상
- **가독성 향상**: 책임 분리로 이해하기 쉬운 코드
- **유지보수성 향상**: 변경 영향 범위 최소화

### 8.2 테스트 용이성
- **단위 테스트 작성 용이**: 각 레이어 독립 테스트
- **Mock 객체 사용 가능**: 인터페이스 기반 설계
- **통합 테스트 간소화**: 명확한 의존성

### 8.3 확장성 향상
- **새로운 기능 추가 용이**: OCP 준수
- **다른 DB 지원 가능**: Repository 패턴
- **다른 뷰 엔진 적용 가능**: 뷰 분리

---

## ⚠️ 9단계: 주의사항

### 9.1 리스크 관리
- **기능 회귀 방지**: 각 단계마다 테스트 실행
- **점진적 마이그레이션**: 기존 코드와 새 코드 병행 운영 가능하게
- **롤백 계획**: 각 단계별 롤백 지점 설정

### 9.2 호환성 유지
- **URL 구조 유지**: 기존 URL 그대로 동작
- **데이터베이스 스키마 유지**: 기존 테이블 구조 유지
- **API 호환성**: 기존 기능 동일하게 동작

---

## 📝 10단계: 다음 단계

리팩토링 완료 후 고려사항:
1. **캐싱 레이어 추가**: 성능 최적화
2. **로깅 시스템**: 에러 추적 및 디버깅
3. **인증/인가 시스템**: 사용자 관리
4. **API 엔드포인트**: RESTful API 제공
5. **컨테이너화**: Docker 지원

---

## 📚 참고 자료

- **리팩토링**: 마틴 파울러
- **클린 코드**: 로버트 C. 마틴
- **PHP The Right Way**: https://phptherightway.com/
- **PSR 표준**: PHP-FIG 표준

---

**작성일**: 2024년  
**버전**: 1.0  
**상태**: 계획 단계
