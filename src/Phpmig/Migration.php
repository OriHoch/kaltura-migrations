<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */


namespace Kmig\Phpmig;

use Kmig\Migrator;
use Phpmig\Migration\Migration as PhpmigMigration;


class Migration extends PhpmigMigration {

    /**
     * @return Migrator
     */
    protected function _migrator()
    {
        $c = $this->getContainer();
        return $c['migrator'];
    }

}
