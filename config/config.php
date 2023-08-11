<?php
//site name
define('SITE_NAME', 'your-site-name');

//DB Params
define('DB_HOST', 'localhost');
define('DB_USER', 'demoweb247_mvc');
define('DB_NAME', 'demoweb247_mvc');
define('DB_PASS', '84XUSnm2wQ');

//App Root
define('SITE_URL', 'https://demoweb247.store/mvc');
define('APP_ROOT', dirname(dirname(__FILE__)));

define('DEFAULT_UPLOAD_DIRECTORY', 'uploads');


// PATH
define('PATH_ROOT', '/');
define('PATH_SUBFOLDER', '/mvc');

define('PATH_VIEWS_CORE', APP_ROOT.'/resources/views/core');
define('PATH_VIEWS', APP_ROOT.'/resources/views');
define('PATH_VIEWS_ADMIN', APP_ROOT.'/resources/views/admin');

// URL
define('URL_PUBLIC_ADMIN', SITE_URL.'/public/admin');