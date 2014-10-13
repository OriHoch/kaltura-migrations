<?php

class AddEntries extends \Kmig\Helper\Phpmig\KmigMigration
{

    public function up()
    {
        $this->_migrator()
            ->entry->add('bar', 'foo')->commit()
        ;
    }

}
