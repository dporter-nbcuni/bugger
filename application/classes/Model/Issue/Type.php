<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Issue_Type extends Model_Base {
    protected $_table_name = 'issue_types';

    protected $_table_columns = array(
        'id' => NULL,
        'name' => NULL
    );
}