<?php
define('ROOT_PATH',         realpath(dirname(__FILE__)));
define('BASE_URL',          'http://localhost/obrien');

// define('DB_HOST',           'localhost');
// define('DB_USER',           'root');
// define('DB_PASS',           '');
// define('DB_NAME',           '');
// define('DB_TABLE',          'obrien');

define('DB_HOST',           'sql6.freesqldatabase.com');
define('DB_USER',           'sql6525734');
define('DB_PASS',           'bs4SfUqIYX');
define('DB_NAME',           '');
define('DB_TABLE',          'sql6525734');
define('DB_CHARSET',          'utf8');
// define('PASSWORD_KEY',          'obrien');

define('TOKEN_SECRET',          'Sana961229');

define('MD5_PRIVATE_KEY',   '2342kuhskdfsd23(&kusdhfjsgJYGJGsfdf384');

const status_order = [
    0 => 'To Ship',
    1 => 'To Receive',
    2 => 'To Confirm',
    3 => 'To Rate',
    4 => 'Completed',
    5 => 'Canceled'
];