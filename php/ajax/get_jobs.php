<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../db_config.php';
require_once __DIR__ . '/../classes/JobListing.php';

$filters = [];

if (!empty($_GET['category_id']))  $filters['category_id']  = (int)$_GET['category_id'];
if (!empty($_GET['location']))     $filters['location']     = $_GET['location'];
if (!empty($_GET['company_name'])) $filters['company_name'] = $_GET['company_name'];
if (!empty($_GET['keyword']))      $filters['keyword']      = $_GET['keyword'];
if (!empty($_GET['show_expired'])) $filters['show_expired'] = $_GET['show_expired'];

$jobListing = new JobListing();
$jobs = $jobListing->getAll($filters);

$result = array_map(function(array $job): array {
    return [
        'id'            => $job['id'],
        'title'         => $job['title'],
        'company_name'  => $job['company_name'],
        'category_name' => $job['category_name'],
        'location'      => $job['location'],
        'deadline'      => $job['deadline'],
        'is_active'     => $job['is_active'],
    ];
}, $jobs);

echo json_encode($result);
