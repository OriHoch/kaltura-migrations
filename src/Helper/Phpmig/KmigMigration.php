<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */


namespace Kmig\Helper\Phpmig;

class KmigMigration extends \Phpmig\Migration\Migration {

    /**
     * @var \Kmig\Container
     */
    protected $_container;

    public function setContainer(\ArrayAccess $container)
    {
        $this->_container = $container;
        return parent::setContainer($container);
    }

    /**
     * Undo the migration - by default use auto migrator down, based on up script
     */
    public function down()
    {
        $this->_autoMigrateDown();
    }

    protected function _autoMigrateDown()
    {
        $this->_migrator()->setDirectionDown();
        $this->up();
    }

    /**
     * @return \Kmig\Migrator
     */
    protected function _migrator()
    {
        $c = $this->getContainer();
        return $c['migrator'];
    }

    /**
     * @return \Kaltura_Client_Client
     */
    protected function _client()
    {
        return $this->_container['client'];
    }

    /**
     * @return \Kmig\Container
     */
    protected function _container()
    {
        return $this->_container;
    }

}
