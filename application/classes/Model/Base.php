<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Model_Base extends ORM {

    public function __get($column)
    {
        $value = $this->get($column);

        switch($column) {
            case 'created_at':
                return date('d/M/y g:i A', strtotime($value));
            case 'updated_at':
                return (strpos($value, '00-00-0000') !== FALSE ? date('d/M/y g:i A', strtotime($value)) : '');
        }

        return $value;
    }
}