<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

namespace Kmig\Helper\Tests;

require_once(__DIR__.'/../../../vendor/autoload.php');
require_once(__DIR__.'/../../../lib/Kaltura/autoload.php');

class BaseTest extends \PHPUnit_Framework_TestCase {

    public static $container;

    protected $_container;

    public static function setUpBeforeClass()
    {
        self::$container = self::getContainer();
    }

    public function setUp()
    {
        $this->_container = self::$container;
    }

    public static function getContainer()
    {
        $container = new \Kmig\Container();
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
        return $container;
    }

    /**
     * @return \Kmig\Migrator
     */
    protected function _migrator()
    {
        return $this->_container['migrator'];
    }

    /**
     * @return \Kaltura_Client_Client
     */
    protected function _client()
    {
        return $this->_container['client'];
    }

}