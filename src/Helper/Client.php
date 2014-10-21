<?php

namespace Kmig\Helper;


class Client {

    /**
     * @param $defaultServerDomain
     * @param $defaultPassword
     * @param $rootClient
     * @return \Kaltura_Client_Type_Partner
     */
    public static function createPublisher($defaultServerDomain, $defaultPassword, $rootClient)
    {
        $partner = new \Kaltura_Client_Type_Partner();
        $name = uniqid();
        $partner->name = $name;
        $partner->adminName = $name;
        $partner->adminEmail = $name.'@'.$defaultServerDomain;
        $partner->description = $name;
        $cmsPassword = $defaultPassword;
        $templatePartnerID = '99';
        return $rootClient->partner->register($partner, $cmsPassword, $templatePartnerID);
    }

    public static function getClient($partnerId, $serviceUrl, $sessionUserId, $adminSecret)
    {
        $config = new \Kaltura_Client_Configuration($partnerId);
        $config->serviceUrl = $serviceUrl;
        $client = new \Kaltura_Client_Client($config);
        $expirySeconds = 86400;
        $privileges = 'disableentitlement';
        $ks = $client->generateSessionV2($adminSecret, $sessionUserId, \Kaltura_Client_Enum_SessionType::ADMIN, $partnerId, $expirySeconds, $privileges);
        $client->setKs($ks);
        return $client;
    }

    public static function getRootClient($serviceUrl, $adminConsoleUser, $adminConsolePassword)
    {
        $config = new \Kaltura_Client_Configuration(null);
        $config->serviceUrl = $serviceUrl;
        $client = new \Kaltura_Client_Client($config);
        $client->user->loginByLoginId($adminConsoleUser, $adminConsolePassword, null, 86400, 'disableentitlement');
        return $client;
    }

    public static function setPartnerPassword($adminConsoleUrl, $adminUser, $adminPassword, $partnerId, $partnerEmail, $newPassword)
    {
        $cmd = ''
            .'casperjs test --no-colors '.escapeshellarg(__DIR__.'/setPartnerPassword.casper.js')
            .' '.escapeshellarg('--adminConsoleUrl='.$adminConsoleUrl)
            .' '.escapeshellarg('--adminUser='.$adminUser)
            .' '.escapeshellarg('--adminPassword='.$adminPassword)
            .' '.escapeshellarg('--partnerId='.$partnerId)
            .' '.escapeshellarg('--partnerEmail='.$partnerEmail)
            .' '.escapeshellarg('--newPassword='.$newPassword)
        ;
        exec($cmd, $output, $returnvar);
        if ($returnvar !== 0) {
            var_dump($cmd);
            throw new \Exception('failed to run!');
        }
    }

} 