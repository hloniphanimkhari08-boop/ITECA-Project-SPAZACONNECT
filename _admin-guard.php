<?php
require_once __DIR__ . '/_guard.php';

if ($currentUser['role'] !== 'admin') {
    api_error('Admin access required.', 403);
}