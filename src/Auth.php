<?php
declare(strict_types=1);

final class Auth
{
    public function __construct(private Database $db)
    {
    }

    public function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function isAdmin(): bool
    {
        return ($this->user()['role'] ?? null) === 'admin';
    }

    public function login(string $email, string $password): bool
    {
        $u = $this->db->fetchOne('SELECT id, name, email, password_hash, role FROM users WHERE email = ?', [$email]);
        if (!$u || !password_verify($password, $u['password_hash'])) {
            return false;
        }
        unset($u['password_hash']);
        $_SESSION['user'] = $u;
        return true;
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
    }

    public function register(string $name, string $email, string $password): array
    {
        $name = trim($name);
        $email = trim($email);

        if ($name === '' || $email === '' || $password === '') {
            return ['ok' => false, 'error' => 'Plotëso të gjitha fushat.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Email jo valid.'];
        }
        if (strlen($password) < 6) {
            return ['ok' => false, 'error' => 'Fjalëkalimi min 6 karaktere.'];
        }
        if ($this->db->fetchOne('SELECT id FROM users WHERE email = ?', [$email])) {
            return ['ok' => false, 'error' => 'Ky email ekziston.'];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->db->exec(
            'INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)',
            [$name, $email, $hash, 'user']
        );

        return ['ok' => true];
    }
}

