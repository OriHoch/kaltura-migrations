<?php

namespace Kmig\Migrator;

class Category extends \Kmig\Base {

    /**
     * @return CategoryObject
     */
    public function add($name, $parentCategoryMigratorId = null)
    {
        $obj = new CategoryObject($this->_container());
        $obj->setName($name);
        if (!empty($parentCategoryMigratorId)) {
            $obj->setParentCategoryMigratorId($parentCategoryMigratorId);
        };
        return $obj;
    }

    public function get($migratorId)
    {
        $id = $this->_migrator()->get('KmigCategory_'.$migratorId);
        return $this->_client()->category->get($id);
    }

    public function set($migratorId, $category)
    {
        $this->_migrator()->set('KmigCategory_'.$migratorId, $category->id);
        return $this;
    }

}

class CategoryObject extends \Kmig\Base {

    protected $_name;
    protected $_parentCategoryMigratorId = null;

    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    public function setParentCategoryMigratorId($id)
    {
        $this->_parentCategoryMigratorId = $id;
        return $this;
    }

    public function commit($migratorId = null)
    {
        if (empty($migratorId)) $migratorId = $this->_name;
        if ($this->_migrator()->isDirectionDown()) {
            $category = $this->_migrator()->category->get($migratorId);
            $this->_client()->category->delete($category->id);
        } else {
            if ($this->_migrator()->exists($migratorId)) throw new \Exception('migrator id already exists');
            $category = new \Kaltura_Client_Type_Category();
            $category->name = $this->_name;
            if (!empty($this->_parentCategoryMigratorId)) {
                $category->parentId = $this->_migrator()->category->get($this->_parentCategoryMigratorId)->id;
            }
            $category = $this->_client()->category->add($category);
            $this->_migrator()->category->set($migratorId, $category);
        }
        return $this->_migrator();
    }

}
