<?php

require_once(__DIR__.'/../lib/Kaltura/autoload.php');
require_once(__DIR__.'/../vendor/autoload.php');

\Kmig\Helper\Client::deletePartner('kaltura.local', 'admin@kaltura.local', 'Kaltura1!', '118');

