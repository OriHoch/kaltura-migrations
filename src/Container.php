<?php

namespace Kmig;

class Container extends \Pimple\Container {

    public function __construct(array $values = array())
    {
        if (empty($values)) $values = array();
        $values = array_merge(array(
            'migrator' => function($c) {
                return new \Kmig\Migrator($c);
            },
            'serviceUrl' => 'http://kaltura.local',
            'adminConsoleUser' => 'admin@kaltura.local',
            'adminConsolePassword' => 'Kaltura1!',
            'defaultServerDomain' => 'kaltura.local',
            'defaultPassword' => 'Kaltura1!',
            'partnerId' => '',
            'partnerAdminSecret' => '',
            'partnerSessionUserId' => 'kmiguser',
            'client' => function($c) {
                if (empty($c['partnerId']) || empty($c['partnerAdminSecret'])) {
                    $partner = Helper\Client::createPublisher($c['defaultServerDomain'], $c['defaultPassword'], $c['rootClient']);
                    $c['partnerId'] = $partner->id;
                    $c['partnerAdminSecret'] = $partner->adminSecret;
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
