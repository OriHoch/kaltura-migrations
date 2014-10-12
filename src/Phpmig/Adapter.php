<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */


namespace Kmig\Phpmig;

use Kmig\Container;
use \Phpmig\Migration\Migration;
use \Phpmig\Adapter\AdapterInterface;

class Adapter implements AdapterInterface {

    /** @var Container */
    protected $_container;

    public function __construct($container)
    {
        $this->_container = $container;
    }

    /**
     * @return \Kaltura_Client_Client
     */
    protected function _client()
    {
        return $this->_container['client'];
    }

    /**
     * @return \Kmig\Migrator
     */
    protected function _migrator()
    {
        return $this->_container['migrator'];
    }

    /**
     * Get all migrated version numbers
     *
     * @return array
     */
    public function fetchAll()
    {
        $versions = $this->_migrator()->get('phpmig_versions');
        sort($versions);
        return $versions;
    }

    /**
     * Up
     *
     * @param Migration $migration
     * @return AdapterInterface
     */
    public function up(Migration $migration)
    {
        $versions = $this->fetchAll();
        if (in_array($migration->getVersion(), $versions)) {
            return $this;
        }
        $versions[] = $migration->getVersion();
        $this->write($versions);
        return $this;
    }

    /**
     * Down
     *
     * @param Migration $migration
     * @return AdapterInterface
     */
    public function down(Migration $migration)
    {
        $versions = $this->fetchAll();
        if (!in_array($migration->getVersion(), $versions)) {
            return $this;
        }
        unset($versions[array_search($migration->getVersion(), $versions)]);
        $this->write($versions);
        return $this;
    }

    /**
     * Is the schema ready?
     *
     * @return bool
     */
    public function hasSchema()
    {
        return $this->_migrator()->exists('phpmig_versions');
    }

    /**
     * Create Schema
     *
     * @return AdapterInterface
     */
    public function createSchema()
    {
        $this->_migrator()->set('phpmig_versions', array());
        return $this;
    }

    /**
     * Write to file
     */
    protected function write($versions)
    {
        $this->_migrator()->set('phpmig_versions', $versions);
    }


} 