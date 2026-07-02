<?php
session_start();
require_once __DIR__ . '/_helpers.php';
require_once __DIR__ . '/../functions.php';

if (empty($_SESSION['user_id'])) {
    api_error('You must be logged in.', 401);
}

$currentUser = $user->findById((int)$_SESSION['user_id']);
if (!$currentUser || !$currentUser['is_active']) {
    api_error('Account not found or inactive.', 401);
}