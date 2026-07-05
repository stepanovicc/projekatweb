<?php

require_once __DIR__ . '/../../db_config.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    public function register(array $data): array {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email already registered.'];
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $token = bin2hex(random_bytes(32));
        $role = ($data['role'] === 'company') ? 'company' : 'user';

        $stmt = $this->db->prepare(
            'INSERT INTO users (email, password, first_name, last_name, role, activation_token)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['email'],
            $hashedPassword,
            $data['first_name'],
            $data['last_name'],
            $role,
            $token
        ]);
        $userId = (int)$this->db->lastInsertId();

        if ($role === 'company') {
            $stmt2 = $this->db->prepare(
                'INSERT INTO companies (user_id, company_name, website, address, description)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt2->execute([
                $userId,
                $data['company_name'] ?? '',
                $data['website'] ?? '',
                $data['address'] ?? '',
                $data['company_description'] ?? ''
            ]);
        }

        return ['success' => true, 'token' => $token, 'user_id' => $userId];
    }

    public function activate(string $token): bool {
        $stmt = $this->db->prepare(
            'UPDATE users SET is_active = 1, activation_token = NULL WHERE activation_token = ?'
        );
        $stmt->execute([$token]);
        return $stmt->rowCount() > 0;
    }

    public function login(string $email, string $password): array|false {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) return false;
        if (!password_verify($password, $user['password'])) return false;
        if (!$user['is_active']) return false;
        if ($user['is_blocked']) return false;

        return $user;
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateProfile(int $id, array $data): bool {
        $fields = [];
        $values = [];

        if (!empty($data['first_name'])) { $fields[] = 'first_name = ?'; $values[] = $data['first_name']; }
        if (!empty($data['last_name']))  { $fields[] = 'last_name = ?';  $values[] = $data['last_name']; }
        if (isset($data['phone']))       { $fields[] = 'phone = ?';      $values[] = $data['phone']; }
        if (isset($data['bio']))         { $fields[] = 'bio = ?';        $values[] = $data['bio']; }

        if (!empty($data['password'])) {
            $fields[] = 'password = ?';
            $values[] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }

        if (empty($fields)) return false;

        $values[] = $id;
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
        return true;
    }

    public function generateResetToken(string $email): array|false {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ? AND is_active = 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user) return false;

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $stmt = $this->db->prepare(
            'UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?'
        );
        $stmt->execute([$token, $expires, $user['id']]);
        return ['token' => $token, 'user_id' => $user['id']];
    }

    public function resetPassword(string $token, string $newPassword): bool {
        $stmt = $this->db->prepare(
            'SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()'
        );
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        if (!$user) return false;

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare(
            'UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?'
        );
        $stmt->execute([$hashed, $user['id']]);
        return true;
    }

    public function getAllUsers(): array {
        $stmt = $this->db->query('SELECT id, email, first_name, last_name, role, is_active, is_blocked, created_at FROM users WHERE role != "admin"');
        return $stmt->fetchAll();
    }

    public function setBlocked(int $id, bool $blocked): bool {
        $stmt = $this->db->prepare('UPDATE users SET is_blocked = ? WHERE id = ?');
        $stmt->execute([(int)$blocked, $id]);
        return $stmt->rowCount() > 0;
    }
}
