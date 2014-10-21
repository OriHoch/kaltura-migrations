<?php

namespace Kmig\Helper\Phpmig;

class KmigAdapter implements \Phpmig\Adapter\AdapterInterface {

    /** @var \Kmig\Container */
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
        if (!$this->_hasSchema()) $this->_createSchema();
        $versions = $this->_migrator()->get('phpmig_versions');
        sort($versions);
        return $versions;
    }

    /**
     * Up
     *
     * @param \Phpmig\Migration\Migration $migration
     * @return \Phpmig\Adapter\AdapterInterface
     */
    public function up(\Phpmig\Migration\Migration $migration)
    {
        if (!$this->_hasSchema()) $this->_createSchema();
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
     * @param \Phpmig\Migration\Migration $migration
     * @return \Phpmig\Adapter\AdapterInterface
     */
    public function down(\Phpmig\Migration\Migration $migration)
    {
        if (!$this->_hasSchema()) $this->_createSchema();
        $versions = $this->fetchAll();
        if (!in_array($migration->getVersion(), $versions)) {
            return $this;
        }
        unset($versions[array_search($migration->getVersion(), $versions)]);
        $this->write($versions);
        return $this;
    }

    public function hasSchema()
    {
        return true;
    }

    public function createSchema()
    {
        return $this;
    }

    public static function setContainerValuesFromDataFile($container, $datafilename)
    {
        $data = json_decode(file_get_contents($datafilename), true);
        $container['serviceUrl'] = $data['serviceUrl'];
        $container['partnerId'] = $data['partnerId'];
        $container['partnerAdminSecret'] = $data['adminSecret'];
        $container['partnerSecret'] = $data['secret'];
        $container['partnerEmail'] = $data['partnerEmail'];
        $container['partnerPassword'] = $data['partnerPassword'];
    }

    /**
     * Is the schema ready?
     *
     * @return bool
     */
    protected function _hasSchema()
    {
        $datafilename = $this->_getDataFileName();
        if (!file_exists($datafilename)) {
            return false;
        } else {
            self::setContainerValuesFromDataFile($this->_container, $datafilename);
            return true;
        }
    }

    /**
     * Create Schema
     *
     * @return \Phpmig\Adapter\AdapterInterface
     */
    protected function _createSchema()
    {
        $data = array();
        $client = $this->_client();
        $data['partnerId'] = $this->_container['partnerId'];
        /** @var \Kaltura_Client_Type_Partner $partner */
        $partner = $client->partner->get($data['partnerId']);
        $data['serviceUrl'] = $this->_container['serviceUrl'];
        $data['adminSecret'] = $this->_container['partnerAdminSecret'];
        $data['secret'] = $partner->secret;
        $data['partnerEmail'] = $this->_container['partnerEmail'];
        $data['partnerPassword'] = $this->_container['partnerPassword'];
        if (!file_put_contents($this->_getDataFileName(), json_encode($data))) {
            throw new \Exception('failed to create data file');
        };
        $this->_migrator()->clear();
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

    protected function _getDataFileName()
    {
        if (!isset($this->_container['Kmig_Phpmig_Adapter_DataFile']) || empty($this->_container['Kmig_Phpmig_Adapter_DataFile'])) {
            throw new \Exception('Kmig_Phpmig_Adapter_DataFile must be set in container');
        }
        return $this->_container['Kmig_Phpmig_Adapter_DataFile'];
    }

} 