<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
    'driver'       => 'orm',
    'hash_method'  => 'sha256',
    'hash_key'     => 'ce85b99cc46752fffee35cab9a7b0278abb4c2d2055cff685af4912c49490f8d',
    'lifetime'     => 1209600,
    'session_type' => Session::$default,
    'session_key'  => 'auth_user',

    // Username/password combinations for the Auth File driver
    'users' => array(
    // 'admin' => 'b3154acf3a344170077d11bdb5fff31532f679a1919e716a02',
    )
);