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
        $category = false;
        $id = $this->_migrator()->get('KmigCategory_'.$migratorId, '');
        if (!empty($id)) {
            try {
                $category = $this->_client()->category->get($id);
            } catch (\Kaltura_Client_Exception $e) {
                if ($e->getCode() != 'CATEGORY_NOT_FOUND') {
                    throw $e;
                }
            }
        }
        return $category;
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
    protected $_attributes = array();

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

    public function setAttributes($attrs)
    {
        $this->_attributes = $attrs;
        return $this;
    }

    public function commit($migratorId = null)
    {
        if (empty($migratorId)) $migratorId = $this->_name;
        if ($this->_migrator()->isDirectionDown()) {
            $this->_migrator()->withoutEntitlement(function($migrator) use($migratorId){
                if ($category = $migrator->category->get($migratorId)) {
                    $migrator->getClient()->category->delete($category->id);
                };
            });
        } else {
            if ($this->_migrator()->exists($migratorId)) throw new \Exception('migrator id already exists');
            $category = new \Kaltura_Client_Type_Category();
            $category->name = $this->_name;
            if (!empty($this->_parentCategoryMigratorId)) {
                $category->parentId = $this->_migrator()->category->get($this->_parentCategoryMigratorId)->id;
            }
            try {
                $category = $this->_client()->category->add($category);
            } catch (\Kaltura_Client_Exception $e) {
                if ($e->getCode() == 'DUPLICATE_CATEGORY') {
                    $filter = new \Kaltura_Client_Type_CategoryFilter();
                    $filter->name = $this->_name;
                    if (!empty($this->_parentCategoryMigratorId)) {
                        $filter->parentIdEqual = $category->parentId;
                    }
                    $res = $this->_client()->category->listAction($filter);
                    $category = $res->objects[0];
                } else {
                    throw $e;
                }
            }
            $this->_migrator()->category->set($migratorId, $category);
            if (!empty($this->_attributes)) {
                $updateCategory = new \Kaltura_Client_Type_Category();
                foreach ($this->_attributes as $k=>$v) {
                    $updateCategory->$k = $v;
                }
                $category = $this->_client()->category->update($category->id, $updateCategory);
            }
        }
        return $this->_migrator();
    }

}
