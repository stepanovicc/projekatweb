<?php

require_once __DIR__ . '/../../db_config.php';

class JobListing {
    private PDO $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    public function getAll(array $filters = [], bool $includeInactive = false): array {
        $where = [];
        $params = [];

        if (!$includeInactive) {
            $where[] = 'jl.is_active = 1';
        }

        if (!empty($filters['category_id'])) {
            $where[] = 'jl.category_id = ?';
            $params[] = (int)$filters['category_id'];
        }
        if (!empty($filters['location'])) {
            $where[] = 'jl.location LIKE ?';
            $params[] = '%' . $filters['location'] . '%';
        }
        if (!empty($filters['company_name'])) {
            $where[] = 'c.company_name LIKE ?';
            $params[] = '%' . $filters['company_name'] . '%';
        }
        if (!empty($filters['keyword'])) {
            $where[] = '(jl.title LIKE ? OR jl.description LIKE ?)';
            $params[] = '%' . $filters['keyword'] . '%';
            $params[] = '%' . $filters['keyword'] . '%';
        }
        if (isset($filters['show_expired'])) {
            if ($filters['show_expired'] === 'expired') {
                $where[] = 'jl.deadline < CURDATE()';
            } elseif ($filters['show_expired'] === 'active') {
                $where[] = 'jl.deadline >= CURDATE()';
            }
        }
        if (!empty($filters['deadline_after'])) {
            $where[] = 'jl.deadline >= ?';
            $params[] = $filters['deadline_after'];
        }

        $sql = 'SELECT jl.*, cat.name AS category_name, c.company_name, u.email AS company_email
                FROM job_listings jl
                JOIN categories cat ON jl.category_id = cat.id
                JOIN companies c ON jl.company_id = c.id
                JOIN users u ON c.user_id = u.id';

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY jl.created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare(
            'SELECT jl.*, cat.name AS category_name, c.company_name, c.website, c.address, c.description AS company_description, u.email AS company_email
             FROM job_listings jl
             JOIN categories cat ON jl.category_id = cat.id
             JOIN companies c ON jl.company_id = c.id
             JOIN users u ON c.user_id = u.id
             WHERE jl.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO job_listings (company_id, category_id, title, description, location, deadline, is_active)
             VALUES (?, ?, ?, ?, ?, ?, 0)'
        );
        $stmt->execute([
            (int)$data['company_id'],
            (int)$data['category_id'],
            $data['title'],
            $data['description'],
            $data['location'],
            $data['deadline']
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getCompanyIdByUserId(int $userId): int|false {
        $stmt = $this->db->prepare('SELECT id FROM companies WHERE user_id = ?');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ? (int)$row['id'] : false;
    }

    public function setActive(int $id, bool $active): bool {
        $stmt = $this->db->prepare('UPDATE job_listings SET is_active = ? WHERE id = ?');
        $stmt->execute([(int)$active, $id]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM job_listings WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public function getByCompany(int $companyId): array {
        $stmt = $this->db->prepare(
            'SELECT jl.*, cat.name AS category_name
             FROM job_listings jl
             JOIN categories cat ON jl.category_id = cat.id
             WHERE jl.company_id = ?
             ORDER BY jl.created_at DESC'
        );
        $stmt->execute([$companyId]);
        return $stmt->fetchAll();
    }
}
