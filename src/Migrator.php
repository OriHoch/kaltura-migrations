<?php

namespace Kmig;

class Migrator extends Base {

    protected $_direction = 'up';

    protected static $_dataCache = array();
    protected static $_dataEntryId = array();

    protected $_withEntitlement = false;
    protected $_userClients = array();

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

    public function get($id, $def = null)
    {
        if ($this->exists($id)) {
            $data = $this->_getData();
            return $data[$id];
        } elseif (is_null($def)) {
            throw new \Exception('migrator id does not exist: '.$id);
        } else {
            return $def;
        }
    }

    public function clear()
    {
        $this->_setDataCache(array());
        $newEntry = new \Kaltura_Client_Type_DataEntry();
        $newEntry->dataContent = json_encode(array());
        if ($this->_getDataEntryId()) {
            $this->_client(true)->data->update($this->_getDataEntryId(), $newEntry);
        } else {
            $newEntry->name = $this->_getDataEntryName();
            $this->_setDataEntryIdCache($this->_client(true)->data->add($newEntry)->id);
        }
        return $this;
    }

    public function withEntitlement($privacyContext, $sessionKey, $callback)
    {
        $curEntitlements = $this->_withEntitlement;
        $this->_withEntitlement = array($privacyContext, $sessionKey);
        $res = $callback($this);
        $this->_withEntitlement = $curEntitlements;
        return $res;
    }

    public function withoutEntitlement($callback)
    {
        $curEntitlements = $this->_withEntitlement;
        $this->_withEntitlement = false;
        $res = $callback($this);
        $this->_withEntitlement = $curEntitlements;
        return $res;
    }

    public function getClient()
    {
        return $this->_client();
    }

    public function set($id, $val)
    {
        $data = $this->_getData();
        $data[$id] = $val;
        $this->_setDataCache($data);
        $newEntry = new \Kaltura_Client_Type_DataEntry();
        $newEntry->dataContent = json_encode($data);
        if ($this->_getDataEntryId()) {
            $this->_client(true)->data->update($this->_getDataEntryId(), $newEntry);
        } else {
            $newEntry->name = $this->_getDataEntryName();
            $this->_setDataEntryIdCache($this->_client(true)->data->add($newEntry)->id);
        }
        return $this;
    }

    public function exists($id)
    {
        $data = $this->_getData();
        return array_key_exists($id, $data);
    }

    public function setDirectionUp()
    {
        $this->_direction = 'up';
        return $this;
    }

    public function setDirectionDown()
    {
        $this->_direction = 'down';
        return $this;
    }

    public function isDirectionDown()
    {
        return ($this->_direction == 'down');
    }

    public static function clearCaches()
    {
        self::$_dataCache = array();
        self::$_dataEntryId = array();
    }

    /**
     * @return \Kaltura_Client_Type_DataEntry
     */
    protected function _getDataEntryId()
    {
        if (is_null($this->_getDataEntryIdCache())) {
            $filter = new \Kaltura_Client_Type_DataEntryFilter();
            $filter->nameEqual = $this->_getDataEntryName();
            $filter->orderBy = \Kaltura_Client_Enum_DataEntryOrderBy::CREATED_AT_DESC;
            $pager = new \Kaltura_Client_Type_FilterPager();
            $pager->pageSize = '1';
            $res = $this->_client(true)->data->listAction($filter, $pager);
            if ($res->totalCount > 0) {
                $dataEntryId = $res->objects[0]->id;
            } else {
                $dataEntryId = false;
            }
            $this->_setDataEntryIdCache($dataEntryId);
            return $dataEntryId;
        } else {
            return $this->_getDataEntryIdCache();
        }
    }

    protected function _getData()
    {
        if (is_null($this->_getDataCache())) {
            $entryId = $this->_getDataEntryId();
            $data = json_decode($this->_client(true)->baseEntry->get($entryId)->dataContent, true);
            $this->_setDataCache($data);
            return $data;
        } else {
            return $this->_getDataCache();
        }
    }

    protected function _getDataCache()
    {
        if (array_key_exists($this->_getCacheId(), self::$_dataCache)) {
            $data = self::$_dataCache[$this->_getCacheId()];
        } else {
            $data = null;
        }
        return $data;
    }

    protected function _getDataEntryIdCache()
    {
        if (array_key_exists($this->_getCacheId(), self::$_dataEntryId)) {
            return self::$_dataEntryId[$this->_getCacheId()];
        } else {
            return null;
        }
    }

    protected function _setDataCache($data)
    {
        self::$_dataCache[$this->_getCacheId()] = $data;
    }

    protected function _setDataEntryIdCache($entryId)
    {
        self::$_dataEntryId[$this->_getCacheId()] = $entryId;
    }

    protected function _getCacheId()
    {
        return $this->_getDataEntryName();
    }

    protected function _getDataEntryName()
    {
        if (!isset($this->_container['Kmig_Migrator_ID']) || empty($this->_container['Kmig_Migrator_ID'])) {
            throw new \Exception('Kmig_Migrator_ID must be set in container');
        }
        return 'Kmig_Migrator_DataEntry_'.$this->_container['Kmig_Migrator_ID'];
    }

    /**
     * @return \Kaltura_Client_Client
     */
    protected function _client($ignoreUserId = false)
    {
        if ($ignoreUserId || !$this->_withEntitlement) {
            return $this->_container['client'];
        } else {
            list($privacyContext, $sessionKey) = $this->_withEntitlement;
            if (!array_key_exists($privacyContext.'::'.$sessionKey, $this->_userClients)) {
                $this->_userClients[$privacyContext.'::'.$sessionKey] = Helper\Client::getClient($this->_container['partnerId'], $this->_container['serviceUrl'], $sessionKey, $this->_container['partnerAdminSecret'], $privacyContext);
            }
            return $this->_userClients[$privacyContext.'::'.$sessionKey];
        }
    }

}