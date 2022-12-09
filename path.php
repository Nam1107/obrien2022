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
    2 => 'To Rate',
    3 => 'Completed',
    4 => 'Canceled'
];

const role = [
    3 => "ROLE_ADMIN",
    2 => "ROLE_SHIPPER",
    1 => "ROLE_USER"
];


const shipping_status = [
    0 => 'Order has been created',
    1 => 'Order is being shipped',
    2 => 'Order has been delivered'
];

const report_status = [
    0 => 'Wrong product delivered',
    1 => 'Defective products'
];

const cancel_reason = [
    0 => 'Changed my mind'
];

const shipping_fail = [
    0 => 'Customers do not answer the phone',
    1 => 'Customers make an appointment to pick up the goods on another day',
    2 => 'Product is being delivered to a wrong address'
];

const order_fail = [
    0 => 'Unable to contact customer',
    1 => 'The goods in stock are sold out',
    2 => 'Product is being delivered to a wrong address'
];

const delivery_status = [
    0 => 'Processing',
    1 => 'Success',
    2 => 'Fail'
];