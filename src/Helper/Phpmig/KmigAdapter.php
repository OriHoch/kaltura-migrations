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
        $datafilename = $this->_getDataFileName();
        if (!file_exists($datafilename)) {
            return false;
        } else {
            $data = json_decode(file_get_contents($datafilename), true);
            $this->_container['serviceUrl'] = $data['serviceUrl'];
            $this->_container['partnerId'] = $data['partnerId'];
            $this->_container['partnerAdminSecret'] = $data['adminSecret'];
            $this->_container['partnerSecret'] = $data['secret'];
            return true;
        }
    }

    /**
     * Create Schema
     *
     * @return \Phpmig\Adapter\AdapterInterface
     */
    public function createSchema()
    {
        $data = array();
        $client = $this->_client();
        $config = $client->getConfig();
        $data['partnerId'] = $config->partnerId;
        $data['serviceUrl'] = $config->serviceUrl;
        /** @var \Kaltura_Client_Type_Partner $partner */
        $partner = $client->partner->get($data['partnerId']);
        $data['adminSecret'] = $partner->adminSecret;
        $data['secret'] = $partner->secret;
        if (!file_put_contents($this->_getDataFileName(), json_encode($data))) {
            throw new \Exception('failed to create data file');
        };
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