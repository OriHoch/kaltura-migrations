<?php

class AddCategories extends \Kmig\Helper\Phpmig\KmigMigration
{

    public function up()
    {
        $this->_migrator()
            ->category->add('root'.uniqid())->commit('root')
            ->category->add('foo')->commit()
        ;
    }

}
