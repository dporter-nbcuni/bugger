<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Issue_Status extends Model_Abstract {
    const OPEN              = 1;
    const REOPENED          = 2;
    const IN_PROGRESS       = 3;
    const READY_FOR_TESTING = 4;
    const RESOLVED          = 5;
    const CLOSED            = 6;
    const ON_HOLD           = 7;
    const SUBMITTED         = 8;

    protected $_table_name = 'issue_statuses';

    protected $_table_columns = array(
        'id' => NULL,
        'name' => NULL,
        'color' => NULL
    );
}