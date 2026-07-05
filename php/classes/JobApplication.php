<?php

require_once __DIR__ . '/../../db_config.php';

class JobApplication {
    private PDO $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    public function apply(int $jobId, int $userId): bool {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO job_applications (job_id, user_id) VALUES (?, ?)'
            );
            $stmt->execute([$jobId, $userId]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function hasApplied(int $jobId, int $userId): bool {
        $stmt = $this->db->prepare(
            'SELECT id FROM job_applications WHERE job_id = ? AND user_id = ?'
        );
        $stmt->execute([$jobId, $userId]);
        return (bool)$stmt->fetch();
    }

    public function getApplicantsForJob(int $jobId): array {
        $stmt = $this->db->prepare(
            'SELECT ja.id, ja.applied_at, u.id AS user_id, u.first_name, u.last_name, u.email, u.phone
             FROM job_applications ja
             JOIN users u ON ja.user_id = u.id
             WHERE ja.job_id = ?
             ORDER BY ja.applied_at DESC'
        );
        $stmt->execute([$jobId]);
        return $stmt->fetchAll();
    }

    public function countByCompany(int $companyId): array {
        $stmt = $this->db->prepare(
            'SELECT jl.id AS job_id, COUNT(ja.id) AS applicant_count
             FROM job_listings jl
             LEFT JOIN job_applications ja ON ja.job_id = jl.id
             WHERE jl.company_id = ?
             GROUP BY jl.id'
        );
        $stmt->execute([$companyId]);

        $counts = [];
        foreach ($stmt->fetchAll() as $row) {
            $counts[(int)$row['job_id']] = (int)$row['applicant_count'];
        }
        return $counts;
    }
}
