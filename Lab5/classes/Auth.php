<?php
class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(int $userId, string $firstName): void
    {
        self::start();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $firstName;
    }

    public static function logout(): void
    {
        self::start();
        session_destroy();
    }

    public static function requireLogin(): void
    {
        self::start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit();
        }
    }

    public static function isAuthenticated(): bool
    {
        self::start();
        return isset($_SESSION['user_id']);
    }

    public static function getUserName(): string
    {
        self::start();
        return htmlspecialchars($_SESSION['user_name'] ?? 'User');
    }

    public static function setFlash(string $message): void
    {
        self::start();
        $_SESSION['flash_message'] = $message;
    }

    public static function getFlash(): ?string
    {
        self::start();

        if (!empty($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }

        return null;
    }
}
