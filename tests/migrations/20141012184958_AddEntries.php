<?php

class AddEntries extends Kmig\Phpmig\Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $this->_migrator()
            ->entry->add('bar', 'foo')->commit()
        ;
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        throw new Exception();
    }
}
