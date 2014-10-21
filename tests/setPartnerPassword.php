<?php

require_once(__DIR__.'/../lib/Kaltura/autoload.php');
require_once(__DIR__.'/../vendor/autoload.php');

$rootClient = \Kmig\Helper\Client::getRootClient('http://kaltura.local', 'admin@kaltura.local', 'Kaltura1!');
/** @var Kaltura_Client_Type_Partner $partner */
$partner = \Kmig\Helper\Client::createPublisher('kaltura.local', 'Kaltura1!', $rootClient);
$config = $rootClient->getConfig();
$url = $config->serviceUrl.'/admin_console';
\Kmig\Helper\Client::setPartnerPassword($url, 'admin@kaltura.local', 'Kaltura1!', $partner->id, $partner->adminEmail, 'Kaltura2!');
echo "
adminEmail: {$partner->adminEmail}
password: Kaltura2!
";