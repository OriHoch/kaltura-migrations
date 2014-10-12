<?php

use Kmig\Helper\Tests\BaseTest;

class MainTest extends BaseTest {

    public function testCategory()
    {
        $uid = uniqid();
        $this->_migrator()
            ->category->add('root'.$uid)->commit('root')
            ->category->add('Foo', 'root')->commit()
            ->category->add('Bar', 'root')->commit()
            ->category->add('Baz', 'Bar')->commit()
            ->entry->add('foo1', 'Foo')->addContentFromFile(__DIR__.'/test.png')->commit()
        ;

        // root category
        $filter = new Kaltura_Client_Type_CategoryFilter();
        $filter->fullNameEqual = 'root'.$uid;
        $res = $this->_client()->category->listAction($filter);
        $this->assertEquals(1, $res->totalCount);
        $category = $res->objects[0];
        $this->assertEquals($category->id, $this->_migrator()->category->get('root')->id);
        /** @var Kaltura_Client_Type_BaseEntry $entry */
        $entry = $this->_migrator()->entry->get('foo1');
        $this->assertEquals($this->_migrator()->category->get('Foo')->id, $entry->categoriesIds);
    }

    public function testEntry()
    {
        $this->_migrator()->entry->add('foo2')->addContentFromFile(__DIR__.'/test.png')->commit();

        $entry = $this->_migrator()->entry->get('foo2');
        $this->assertEquals('foo2', $this->_client()->baseEntry->get($entry->id)->name);
        $this->assertTrue(file_get_contents(__DIR__.'/test.png') == file_get_contents($entry->downloadUrl));
    }

    public function testStorage()
    {
        $this->_migrator()->set('test', '123456');
        $this->assertEquals('123456', $this->_migrator()->get('test'));
        // get a new container but with the same client from previous container
        $client = $this->_client();
        $container = self::getContainer();
        $container['client'] = $client;
        // now, make sure the new migrator still knows about our value
        $this->assertEquals('123456', $container['migrator']->get('test'));
    }

}
