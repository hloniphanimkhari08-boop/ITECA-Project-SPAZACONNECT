<?php
require_once __DIR__ . '/_guard.php';

if (!in_array($currentUser['role'], ['seller', 'admin'])) {
    api_error('Seller account required.', 403);
}