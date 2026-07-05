<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../db_config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../classes/JobListing.php';
require_once __DIR__ . '/../classes/JobApplication.php';

if (!isLoggedIn() || isAdmin() || isCompany()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only job seekers can apply to jobs.']);
    exit;
}

$input     = json_decode(file_get_contents('php://input'), true) ?? [];
$jobId     = (int)($input['job_id'] ?? 0);
$csrfToken = $input['csrf_token'] ?? '';

if (!verifyCsrfToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid request. Please refresh and try again.']);
    exit;
}

$job = (new JobListing())->getById($jobId);
if (!$job || !$job['is_active'] || strtotime($job['deadline']) < time()) {
    echo json_encode(['success' => false, 'message' => 'This job listing is no longer accepting applications.']);
    exit;
}

$applicationObj = new JobApplication();
$userId         = (int)$_SESSION['user_id'];

if ($applicationObj->hasApplied($jobId, $userId)) {
    echo json_encode(['success' => false, 'message' => 'You already applied to this job.', 'alreadyApplied' => true]);
    exit;
}

$applied = $applicationObj->apply($jobId, $userId);
echo json_encode([
    'success' => $applied,
    'message' => $applied ? 'Application submitted! The company can now contact you by email.' : 'Failed to submit application. Please try again.',
]);
