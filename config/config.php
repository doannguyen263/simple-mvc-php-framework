<?php
//site name
define('SITE_NAME', 'your-site-name');

//DB Params
define('DB_HOST', 'localhost');
define('DB_USER', 'admin');
define('DB_PASS', '123456');
define('DB_NAME', 'test_codethuan_mvc');

//App Root
define('SITE_URL', 'http://localhost/codethuan/simple-mvc-php-framework');
define('APP_ROOT', dirname(dirname(__FILE__)));

// PATH
define('PATH_ROOT', '/');
define('PATH_SUBFOLDER', '/codethuan/simple-mvc-php-framework');

define('PATH_VIEWS_CORE', APP_ROOT.'/resources/views/core');
define('PATH_VIEWS', APP_ROOT.'/resources/views');
define('PATH_VIEWS_ADMIN', APP_ROOT.'/resources/views/admin');

// URL
define('URL_PUBLIC_ADMIN', SITE_URL.'/public/admin');