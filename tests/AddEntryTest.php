<?php

require_once(__DIR__.'/../vendor/autoload.php');
require_once(__DIR__.'/../lib/Kaltura/autoload.php');

use Pimple\Container;
use Kmig\Migrator;

class AddEntryTest extends PHPUnit_Framework_TestCase {

    public function test()
    {
        $container = new Container();
        $container['client'] = function($c) {
            return Kmig\Tests\Helper::getAdminClient();
        };
        $container['migrator'] = function($c) {
            return new Migrator($c);
        };
        /** @var Migrator $migrator */
        $uid = uniqid();
        $migrator = $container['migrator'];
        $migrator
            ->category->add('root'.$uid)->commit('root')
            ->category->add('Foo', 'root')->commit()
            ->category->add('Bar', 'root')->commit()
            ->category->add('Baz', 'Bar')->commit()
            ->entry->add('foo1', 'Foo')->addContentFromFile(__DIR__.'/test.png')->commit()
        ;
        $this->assertEquals(
            'root'.$uid,
            $container['client']->category->get($migrator->category->get('root')->id)->name
        );
        $this->assertEquals(
            'foo1',
            $container['client']->baseEntry->get($migrator->entry->get('foo1')->id)->name
        );
    }

}
