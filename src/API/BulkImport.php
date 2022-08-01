<?php

namespace Flits\MoEngage\API;

use Flits\MoEngage\MoEngageProvider;

class BulkImport extends MoEngageProvider {

    public $URL = "transition/<APP_ID>";
    public $METHOD = "POST";

    function __construct($config) {
        parent::__construct($config);
    }
}