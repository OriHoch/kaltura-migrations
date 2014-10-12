<?php

require_once('../lib/Kaltura/autoload.php');

use \Phpmig\Adapter;

$container = new Kmig\Container();
$serviceUrl = getenv('KALTURA_SERVICE_URL');
$partnerId = getenv('KALTURA_PARTNER_ID');
$adminSecret = getenv('KALTURA_ADMIN_SECRET');
if (!empty($serviceUrl)) {
    $container['serviceUrl'] = $serviceUrl;
}
if (!empty($partnerId) && !empty($adminSecret)) {
    $container['partnerId'] = $partnerId;
    $container['partnerAdminSecret'] = $adminSecret;
}

$container['migrator'] = function($c) {
    return new Kmig\Migrator($c);
};

$container['phpmig.adapter'] = function($c) {
    return new \Kmig\Phpmig\Adapter($c);
};

$container['phpmig.migrations_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';

return $container;