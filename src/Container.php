<?php

namespace Kmig;

class Container extends \Pimple\Container {

    public function __construct(array $values = array())
    {
        if (empty($values)) $values = array();
        $serviceUrl = getenv('KALTURA_SERVICE_URL');
        $partnerId = getenv('KALTURA_PARTNER_ID');
        $adminSecret = getenv('KALTURA_ADMIN_SECRET');
        $adminConsoleUser = getenv('KALTURA_ADMIN_CONSOLE_USER');
        $adminConsolePassword = getenv('KALTURA_ADMIN_CONSOLE_PASSWORD');
        $defaultServerDomain = getenv('KALTURA_DEFAULT_SERVER_DOMAIN');
        $defaultPassword = getenv('KALTURA_DEFAULT_PASSWORD');
        $partnerEmail = getenv('KALTURA_PARTNER_EMAIL');
        $partnerPassword = getenv('KALTURA_PARTNER_PASSWORD');
        $values = array_merge(array(
            'migrator' => function($c) {
                return new \Kmig\Migrator($c);
            },
            'phpmig.adapter' => function($c) {
                return new \Kmig\Helper\Phpmig\KmigAdapter($c);
            },
            'serviceUrl' => empty($serviceUrl) ? 'http://kaltura.local' : $serviceUrl,
            'adminConsoleUser' => empty($adminConsoleUser) ? 'admin@kaltura.local' : $adminConsoleUser,
            'adminConsolePassword' => empty($adminConsolePassword) ? 'Kaltura1!' : $adminConsolePassword,
            'defaultServerDomain' => empty($defaultServerDomain) ? 'kaltura.local' : $defaultServerDomain,
            'defaultPassword' => empty($defaultPassword) ? 'Kaltura1!' : $defaultPassword,
            'partnerId' => empty($partnerId) ? '' : $partnerId,
            'partnerAdminSecret' => empty($adminSecret) ? '' : $adminSecret,
            'partnerSessionUserId' => 'kmiguser',
            'partnerEmail' => empty($partnerEmail) ? '' : $partnerEmail,
            'partnerPassword' => empty($partnerPassword) ? '' : $partnerPassword,
            'client' => function($c) {
                if (empty($c['partnerId']) || empty($c['partnerAdminSecret']) || empty($c['partnerEmail']) || empty($c['partnerPassword'])) {
                    $partner = Helper\Client::createPublisher($c['defaultServerDomain'], $c['defaultPassword'], $c['rootClient']);
                    $c['partnerId'] = $partner->id;
                    $c['partnerAdminSecret'] = $partner->adminSecret;
                    $c['partnerEmail'] = $partner->adminEmail;
                    $c['partnerPassword'] = $c['defaultPassword'];
                    Helper\Client::setPartnerPassword($c['serviceUrl'].'/admin_console', $c['adminConsoleUser'], $c['adminConsolePassword'], $c['partnerId'], $c['partnerEmail'], $c['defaultPassword']);
                }
                return Helper\Client::getClient($c['partnerId'], $c['serviceUrl'], $c['partnerSessionUserId'], $c['partnerAdminSecret']);
            },
            'rootClient' => function($c) {
                return Helper\Client::getRootClient($c['serviceUrl'], $c['adminConsoleUser'], $c['adminConsolePassword']);
            }
        ), $values);
        parent::__construct($values);
    }

}
