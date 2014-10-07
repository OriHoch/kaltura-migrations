<?php

namespace Kmig\Tests;

class Helper {

    public static function getClient($type)
    {
        $partnerId = getenv('KALTURA_PARTNER_ID');
        if (empty($partnerId) || !is_numeric($partnerId)) {
            throw new \Exception('you must set the KALTURA_PARTNER_ID environment variable');
        }
        $config = new \Kaltura_Client_Configuration($partnerId);
        $config->serviceUrl = getenv('KALTURA_SERVICE_URL') ? getenv('KALTURA_SERVICE_URL') : 'http://www.kaltura.com';
        $client = new \Kaltura_Client_Client($config);
        $adminSecret = getenv('KALTURA_ADMIN_SECRET');
        if (empty($adminSecret)) {
            throw new \Exception('you must set the KALTURA_ADMIN_SECRET environment variable');
        }
        $expirySeconds = 60*60*4;
        $privileges = 'disableentitlement';
        $userId = '';
        $ks = $client->generateSessionV2($adminSecret, $userId, $type, $partnerId, $expirySeconds, $privileges);
        $client->setKs($ks);
        return $client;
    }

    public static function getAdminClient()
    {
        return self::getClient(\Kaltura_Client_Enum_SessionType::ADMIN);
    }

    public static function getUserClient()
    {
        return self::getClient(\Kaltura_Client_Enum_SessionType::USER);
    }

}
