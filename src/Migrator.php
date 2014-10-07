<?php

namespace Kmig;

class Migrator extends Base {

    protected $_storage = array();

    /** @var Migrator\Entry */
    public $entry;

    /** @var Migrator\Category */
    public $category;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->entry = new Migrator\Entry($container);
        $this->category = new Migrator\Category($container);
    }

    public function get($id)
    {
        return $this->_storage[$id];
    }

    public function set($id, $val)
    {
        $this->_storage[$id] = $val;
        return $this;
    }

    public function exists($id)
    {
        return array_key_exists($id, $this->_storage);
    }

}