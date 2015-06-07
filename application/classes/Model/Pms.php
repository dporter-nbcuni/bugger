<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Pms extends Model_Abstract {
    const JIRA        = 1;
    const BASECAMP    = 2;
    const LIGHTHOUSE  = 3;
    const RALLY       = 4;

    protected $_table_name = 'pms';

    protected $_table_columns = array(
        'id' => NULL,
        'name' => NULL
    );
}