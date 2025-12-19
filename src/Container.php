<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Repository/PostRepository.php';
require_once __DIR__ . '/Repository/CommentRepository.php';
require_once __DIR__ . '/Validator/PostValidator.php';
require_once __DIR__ . '/Validator/CommentValidator.php';
require_once __DIR__ . '/Service/PostService.php';
require_once __DIR__ . '/Service/CommentService.php';
require_once __DIR__ . '/Controller/PostController.php';
require_once __DIR__ . '/Controller/CommentController.php';

/**
 * 간단한 의존성 주입 컨테이너
 */
class Container {
    private static ?PDO $pdo = null;
    private static ?PostRepository $postRepository = null;
    private static ?CommentRepository $commentRepository = null;
    private static ?PostValidator $postValidator = null;
    private static ?CommentValidator $commentValidator = null;
    private static ?PostService $postService = null;
    private static ?CommentService $commentService = null;
    private static ?PostController $postController = null;
    private static ?CommentController $commentController = null;
    
    public static function getPDO(): PDO {
        if (self::$pdo === null) {
            self::$pdo = DatabaseConfig::getConnection();
        }
        return self::$pdo;
    }
    
    public static function getPostRepository(): PostRepository {
        if (self::$postRepository === null) {
            self::$postRepository = new PostRepository(self::getPDO());
        }
        return self::$postRepository;
    }
    
    public static function getCommentRepository(): CommentRepository {
        if (self::$commentRepository === null) {
            self::$commentRepository = new CommentRepository(self::getPDO());
        }
        return self::$commentRepository;
    }
    
    public static function getPostValidator(): PostValidator {
        if (self::$postValidator === null) {
            self::$postValidator = new PostValidator();
        }
        return self::$postValidator;
    }
    
    public static function getCommentValidator(): CommentValidator {
        if (self::$commentValidator === null) {
            self::$commentValidator = new CommentValidator();
        }
        return self::$commentValidator;
    }
    
    public static function getPostService(): PostService {
        if (self::$postService === null) {
            self::$postService = new PostService(
                self::getPostRepository(),
                self::getCommentRepository(),
                self::getPostValidator()
            );
        }
        return self::$postService;
    }
    
    public static function getCommentService(): CommentService {
        if (self::$commentService === null) {
            self::$commentService = new CommentService(
                self::getCommentRepository(),
                self::getPostRepository(),
                self::getCommentValidator()
            );
        }
        return self::$commentService;
    }
    
    public static function getPostController(): PostController {
        if (self::$postController === null) {
            self::$postController = new PostController(
                self::getPostService(),
                self::getCommentService()
            );
        }
        return self::$postController;
    }
    
    public static function getCommentController(): CommentController {
        if (self::$commentController === null) {
            self::$commentController = new CommentController(
                self::getCommentService()
            );
        }
        return self::$commentController;
    }
}
