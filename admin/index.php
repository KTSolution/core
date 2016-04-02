<?php
// Version
define('VERSION', '0.1');
//define('DIR_SYSTEM', str_replace('\'', '/', realpath(dirname(__FILE__) . '/')) . 'system/');
define('DIR_ROOT', '../');
define('DIR_SITE', 'admin/');


// Configuration
if (is_file('../config.php')) {
    require_once('../config.php');
}

// Constant
if (is_file('../constant.php')) {
    require_once('../constant.php');
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// init class
$registry = new Registry();
$loader = new Loader($registry);
$config = new Config();
$request = new Request();
$response = new Response();
$cache = new Cache('file');
$session = new Session();
$document = new Document();


// Config

$registry->set('config', $config);

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
$registry->set('db', $db);

// Domain
$config->set('config_url', HTTP_SERVER);
$config->set('config_ssl', HTTPS_SERVER);

// Settings
$mSetting = $loader->eloquent('Setting');
$settings = $mSetting::all();
foreach ($settings as $result) {
    if (!$result['serialized']) {
        $config->set($result['key'], $result['value']);
    } else {
        $config->set($result['key'], json_decode($result['value'], true));
    }
}
// Log
$log = new Log($config->get('config_error_filename'));
function error_handler($code, $message, $file, $line) {
    global $log, $config;

    // error suppressed with @
    if (error_reporting() === 0) {
        return false;
    }

    switch ($code) {
        case E_NOTICE:
        case E_USER_NOTICE:
            $error = 'Notice';
            break;
        case E_WARNING:
        case E_USER_WARNING:
            $error = 'Warning';
            break;
        case E_ERROR:
        case E_USER_ERROR:
            $error = 'Fatal Error';
            break;
        default:
            $error = 'Unknown';
            break;
    }

    if ($config->get('config_error_display')) {
        echo '<b>' . $error . '</b>: ' . $message . ' in <b>' . $file . '</b> on line <b>' . $line . '</b>';
    }

    if ($config->get('config_error_log')) {
        $log->write('PHP ' . $error . ':  ' . $message . ' in ' . $file . ' on line ' . $line);
    }

    return true;
}
set_error_handler('error_handler');

// Url
$url = new Url($request, $config->get('config_url'), $config->get('config_ssl'));
$route = $url->currentAdmin();

// Languages
$languages = array();
$mLanguage = $loader->eloquent("Language");
$langList = $mLanguage::where('enable', '=', '1')->get();

foreach ($langList as $lang) {
    $languages[$lang['code']] = $lang;
//    $select_lang[] = array($lang['code'],$lang['name']);
}

$language_code = isset($session->data['language']) ? $session->data['language'] : $config->get('config_language');

if (!isset($session->data['language']) || $session->data['language'] != $language_code) {
    $session->data['language'] = $language_code;
}

if (!isset($request->cookie['language']) || $request->cookie['language'] != $language_code) {
    setcookie('language', $language_code, time() + 60 * 60 * 24 * 30, '/', $request->server['HTTP_HOST']);
}

if ( empty($config->get('config_directory')) || ($config->get('config_directory') != $languages[$language_code]['directory'])) {
    $config->set('config_directory', $languages[$language_code]['directory']);
}

// Language
$language = new Language($config->get('config_directory'));
$language->set('current', $language_code);

// document
$data['direction'] = $config->get('config_directory');
$data['lang'] = $language_code;


$registry->set('load', $loader);
$registry->set('config', $config);
$registry->set('db', $db);
$registry->set('log', $log);
$registry->set('request', $request);
$registry->set('url', $url);
$registry->set('response', $response);
$registry->set('cache', $cache);
$registry->set('session', $session);
$registry->set('document', $document);
$registry->set('language', $language);
$registry->set('response', $response);

if(1!=1){
    $templatePath = "login.html";

    $post['username'] = isset($request->post['login-user']) ? $request->post['login-user'] : "";
    $post['password'] = isset($request->post['login-pass']) ? $request->post['login-pass'] : "";
    $auth = $loader->model('Auth');
    $user = $auth->getAccountInfo($post);
    $language->load('website/login');
    $data['title'] = $language->get('lang_site_title');
    $data['LANG_SIGN_IN'] = $language->get('lang_sign_in_header');
    $data['langList'] = render_html_element($select_lang, ELEMENT_TYPE_SELECT);
    if( $user->perm == USER_ADMIN ) {
        $request->cookie['usess'] = $user;
        $templatePath = "master.html";
    }

} else {
    //$user = $request->cookie['usess'];
    $templatePath = "master.html";
}

if(!empty($route)){
    $data = $loader->json($route);
    $response->addHeader('Content-Type: application/json');
    $response->setJsonOutput($data);

} else {
    $fonts = $loader->file('fonts');
    $setting = array(
        "name" => "ktsSetting",
        "type" => "inline",
        "data" => array(
            "ngKTSPath" => array(
                "template" =>"/admin/view/",
                "action" => "/admin/",
                "base_url" => "http://demo.local/admin/"
            ),
            "tinyMCEFonts" => implode(";", $fonts['font'])
        )
    );

    $document->addScript($setting);
//    $document->addScript("javascript/app/menu.js");
//    $document->addScript("javascript/app/redirect.js");
//    $document->addScript("javascript/app/app.js");
//    $document->addScript("javascript/app/general.js");
//    $document->addScript("javascript/app/directive.js");
//    $document->addScript("javascript/app/dashboard.js");
//    $document->addScript("javascript/app/website/setting.js");
//    $document->addScript("javascript/app/website/language.js");
//    $document->addScript("javascript/app/misc/member.js");
    $response->addHeader('Content-Type', 'text/html; charset=utf-8');
    //$response->setCompression($config->get('config_compression'));
    $response->setOutput($loader->view( $templatePath , $document->getData()));
}

$response->output();