<?php

class AddCategories extends Kmig\Phpmig\Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $this->_migrator()
            ->category->add('root'.uniqid())->commit('root')
            ->category->add('foo')->commit()
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
