<?php

namespace Kmig\Migrator;

class Entry extends \Kmig\Base {

    /**
     * @return EntryObject
     */
    public function add($name, $categoryMigratorId = null)
    {
        $obj = new EntryObject($this->_container());
        return $obj->setName($name)->setCategoryMigratorId($categoryMigratorId);
    }

    public function get($migratorId)
    {
        $id = $this->_migrator()->get('KmigEntry_'.$migratorId);
        return $this->_client()->baseEntry->get($id);
    }

    public function set($migratorId, $entry)
    {
        $this->_migrator()->set('KmigEntry_'.$migratorId, $entry->id);
        return $this;
    }

}

class EntryObject extends \Kmig\Base {

    protected $_name;

    protected $_categoryMigratorId = null;

    protected $_contentFilename = null;

    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    public function setCategoryMigratorId($id)
    {
        $this->_categoryMigratorId = $id;
        return $this;
    }

    public function addContentFromFile($filename)
    {
        $this->_contentFilename = $filename;
        return $this;
    }

    public function commit($migratorId = null)
    {
        if (empty($migratorId)) $migratorId = $this->_name;
        if ($this->_migrator()->isDirectionDown()) {
            $entry = $this->_migrator()->entry->get($migratorId);
            $this->_client()->baseEntry->delete($entry->id);
        } else {
            if ($this->_migrator()->exists($migratorId)) throw new \Exception('migrator id already exists');
            $entry = new \Kaltura_Client_Type_BaseEntry();
            $entry->name = $this->_name;
            if (!empty($this->_categoryMigratorId)) {
                $entry->categoriesIds = $this->_migrator()->category->get($this->_categoryMigratorId)->id;
            }
            $entry = $this->_client()->baseEntry->add($entry);
            if (!empty($this->_contentFilename)) {
                $filename = $this->_contentFilename;
                $kfilename = uniqid();
                $uploadToken = new \Kaltura_Client_Type_UploadToken();
                $uploadToken->fileName = $kfilename;
                $uploadToken->fileSize = filesize($filename);
                $uploadToken = $this->_client()->uploadToken->add($uploadToken);
                $uploadToken = $this->_client()->uploadToken->upload($uploadToken->id, $filename);
                if ($uploadToken->uploadedFileSize != filesize($filename)) {
                    throw new \Exception('failed to upload file');
                }
                $resource = new \Kaltura_Client_Type_UploadedFileTokenResource();
                $resource->token = $uploadToken->id;
                $this->_client()->baseEntry->addContent($entry->id, $resource);
            }
            $this->_migrator()->entry->set($migratorId, $entry);
        }
        return $this->_migrator();
    }

}