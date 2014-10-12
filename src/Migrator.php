<?php

namespace Kmig;

class Migrator extends Base {

    protected $_dataCache = false;
    protected $_dataEntry = false;

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
        $data = $this->_getData();
        return $data[$id];
    }

    public function set($id, $val)
    {
        $dataEntry = $this->_getDataEntry();
        $data = $this->_getData($dataEntry);
        $data[$id] = $val;
        $newEntry = new \Kaltura_Client_Type_DataEntry();
        $newEntry->dataContent = json_encode($data);
        if (!empty($dataEntry)) {
            $this->_client()->data->update($dataEntry->id, $newEntry);
        } else {
            $newEntry->name = 'Kmig_Migrator_DataEntry';
            $this->_dataEntry = $this->_client()->data->add($newEntry);
        }
        $this->_dataCache = $data;
        return $this;
    }

    public function exists($id)
    {
        $data = $this->_getData();
        return array_key_exists($id, $data);
    }

    /**
     * @return \Kaltura_Client_Type_DataEntry
     */
    protected function _getDataEntry()
    {
        if ($this->_dataEntry === false) {
            $filter = new \Kaltura_Client_Type_DataEntryFilter();
            $filter->nameEqual = 'Kmig_Migrator_DataEntry';
            $filter->orderBy = \Kaltura_Client_Enum_DataEntryOrderBy::CREATED_AT_DESC;
            $pager = new \Kaltura_Client_Type_FilterPager();
            $pager->pageSize = '1';
            $res = $this->_client()->data->listAction($filter, $pager);
            if ($res->totalCount > 0) {
                $this->_dataEntry = $res->objects[0];
            } else {
                $this->_dataEntry = null;
            }
        }
        return $this->_dataEntry;
    }

    protected function _getData($dataEntry = null)
    {
        if ($this->_dataCache === false) {
            $dataEntry = empty($dataEntry) ? $this->_getDataEntry() : $dataEntry;
            if (!empty($dataEntry)) {
                $data = json_decode($dataEntry->dataContent, true);
            } else {
                $data = array();
            }
            $this->_dataCache = $data;
        }
        return $this->_dataCache;
    }

}