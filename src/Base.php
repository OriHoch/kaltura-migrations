<?php

namespace Kmig;

abstract class Base {

    /** @var \Pimple\Container */
    protected $_container;

    public function __construct($container)
    {
        $this->_container = $container;
    }

    /**
     * @return \Pimple\Container
     */
    protected function _container()
    {
        return $this->_container;
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

}
