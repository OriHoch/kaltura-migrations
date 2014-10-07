<?php

namespace Kmig;

class Base {

    /** @var \Pimple\Container */
    protected $_container;

    /** @var  \Kaltura_Client_Client */
    protected $_client;

    public function __construct($container)
    {
        $this->_container = $container;
        $this->_client = $container['client'];
    }

    public function __get($k)
    {
        if ($k == '_migrator') {
            return $this->_container['migrator'];
        } else {
            throw new \Exception();
        }
    }

}