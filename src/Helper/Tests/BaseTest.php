<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

namespace Kmig\Helper\Tests;

use Kmig\Migrator;

require_once(__DIR__.'/../../../vendor/autoload.php');
require_once(__DIR__.'/../../../lib/Kaltura/autoload.php');

class BaseTest extends \PHPUnit_Framework_TestCase {

    public static $client;

    protected $_container;

    public static function setUpBeforeClass()
    {
        $container = new \Kmig\Container();
        self::$client = $container['client'];
    }

    public function setUp()
    {
        $this->_container = new \Kmig\Container(array(
            'client' => self::$client,
            'Kmig_Migrator_ID' => 'Kmig_BaseTest',
        ));
    }

    public static function tearDownAfterClass()
    {
        Migrator::clearCaches();
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
