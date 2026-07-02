<?php
require_once __DIR__ . '/db/config.php';
require_once __DIR__ . '/db/User.php';
require_once __DIR__ . '/db/Product.php';
require_once __DIR__ . '/db/Cart.php';
require_once __DIR__ . '/db/Order.php';
require_once __DIR__ . '/db/Address.php';
require_once __DIR__ . '/db/Category.php';
require_once __DIR__ . '/db/Review.php';
require_once __DIR__ . '/db/Payment.php';

$db       = new Config();
$user     = new User($db);
$product  = new Product($db);
$cart     = new Cart($db);
$order    = new Order($db);
$address  = new Address($db);
$category = new Category($db);
$review   = new Review($db);
$payment  = new Payment($db);

define('BASE_URL', 'https://spaza-connect.gt.tc');

function upload_image(array $file, string $folder = '/assets/img/products/'): string|false {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed))           return false;
    if ($file['size'] > 5 * 1024 * 1024)    return false;
    if (!is_uploaded_file($file['tmp_name'])) return false;

    $filename = uniqid('img_') . '.' . $ext;

    // Fix: Clean out the web domain string from the physical disk lookup path
    $cleanFolder = '/' . ltrim($folder, '/');
    $absoluteUploadDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $cleanFolder;

    if (!is_dir($absoluteUploadDir)) {
        mkdir($absoluteUploadDir, 0755, true);
    }

    $physicalDestination = rtrim($absoluteUploadDir, '/') . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $physicalDestination)) {
        // Return a clean web asset URL address path for the database cell entry
        return BASE_URL . $cleanFolder . $filename;
    }

    return false;
}

// SandboxPayFast Configuration
define('PF_MERCHANT_ID',  '10049608');
define('PF_PASSPHRASE',   'spazaConnect');
define('PF_MERCHANT_KEY', 'qijcqbqdh2yqp');
define('PF_SANDBOX',      true); // Set to false for Live
define('PF_URL_SANDBOX',  'https://sandbox.payfast.co.za/eng/process');
define('PF_URL_LIVE',     'https://www.payfast.co.za/eng/process');

// Generate PayFast signature for outbound checkout forms
function pf_signature(array $data, string $passphrase = ''): string
{
    unset($data['signature']);
    $parts = [];

    foreach ($data as $key => $val) {
        if ($val !== '' && $val !== null) {
            $parts[] = $key . '=' . urlencode(trim((string)$val));
        }
    }

    $str = implode('&', $parts);
    if ($passphrase !== '') {
        $str .= '&passphrase=' . urlencode(trim($passphrase));
    }

    return md5($str);
}

// Validate PayFast signature for incoming ITN notifications
function pf_validate_signature(array $data, string $receivedSignature, string $passphrase = ''): bool
{
    $pfParamString = '';

    foreach ($data as $key => $val) {
        $data[$key] = stripslashes($val);
    }

    foreach ($data as $key => $val) {
        if ($key !== 'signature') {
            $pfParamString .= $key . '=' . urlencode($val) . '&';
        } else {
            break;
        }
    }

    $pfParamString = substr($pfParamString, 0, -1);

    if ($passphrase !== '') {
        $pfParamString .= '&passphrase=' . urlencode(trim($passphrase));
    }

    $calculatedSignature = md5($pfParamString);
    return hash_equals($calculatedSignature, $receivedSignature);
}

// Helper to get the correct URL
function pf_get_url(): string {
    return PF_SANDBOX ? PF_URL_SANDBOX : PF_URL_LIVE;
}