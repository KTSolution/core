<?php

// init class
$registry = new Registry();
$loader = new Loader($registry);
$config = new Config();
$request = new Request();
$response = new Response();
$cache = new Cache('file');
$session = new Session();
$document = new Document();

$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

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

// Languages
$languages = array();
$mLanguage = $loader->eloquent("Language");
$langList = $mLanguage::where('enable', '=', '1')->get();

// Pages
$pages = array();
$mPage = $loader->eloquent('Page');
$pageList = $mPage::where('enable', '=', '1')->get();

foreach ($langList as $lang) {
    $languages[$lang->code] = $lang;
    foreach ($pageList as $page) {
        $page->unique = get_lang_text($page->code, $lang->code);
        if(empty($page->unique)) continue;
        $p = new stdClass();
        $p->id = $page->id;
        $p->code = $page->code;
        $p->unique = $page->unique;
        $p->title = get_lang_text($page->title, $lang->code);
        $p->description = get_lang_text($page->description, $lang->code);
        $p->keyword = get_lang_text($page->keyword, $lang->code);
        $p->robot = get_lang_text($page->keyword, $lang->code);
        $p->lang = $lang->code;

        $pages[$page->unique] = $p;
        unset($page);
    }
}

// Url
$url = new Url($request, $config->get('config_url'), $config->get('config_ssl'));
$url->addRewrite($pages);
$route = $url->current();
$routes = $loader->file('routes');
$findPage = function() use ($pages, $route, &$language_code, $session, $config) {
    if(in_array($route, array_keys($pages))){
        $currPage = $pages[$route];
        $language_code = $currPage->lang;
    } else {
        $language_code = isset($session->data['language']) ? $session->data['language'] : $config->get('config_language');
        $page_id = empty($route) ? PAGE_HOME : PAGE_ERROR;
        foreach ($pages as $page) {
            if ($page->lang == $language_code && $page->id == $page_id) {
                $currPage = $page;
            }
        }
    }
    return $currPage;
};
$currPage = $findPage();
$route = $routes[$currPage->id];

/*
if (isset($session->data['language']) && array_key_exists($session->data['language'], $languages)) {
    $code = $session->data['language'];
} elseif (isset($request->cookie['language']) && array_key_exists($request->cookie['language'], $languages)) {
    $code = $request->cookie['language'];
} else {
    $detect = '';
    if (isset($request->server['HTTP_ACCEPT_LANGUAGE']) && $request->server['HTTP_ACCEPT_LANGUAGE']) {
        $browser_languages = explode(',', $request->server['HTTP_ACCEPT_LANGUAGE']);

        foreach ($browser_languages as $browser_language) {
            foreach ($languages as $key => $value) {
                if ($value['status']) {
                    $locale = explode(',', $value['locale']);

                    if (in_array($browser_language, $locale)) {
                        $detect = $key;
                        break 2;
                    }
                }
            }
        }
    }

    $code = $detect ? $detect : $config->get('config_language');
}
*/

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

//$document->setBase($config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url'));
$document->setTitle($currPage->title . " | " . $config->get('config_name'));
$document->setDescription($currPage->description);
$document->setKeywords($currPage->keyword);
$document->setLanguage(array("code"=>$language_code, "direction"=>$config->get('config_directory')));

/*
// Customer
if(PACKAGE_CUSTOMER) {
    $customer = new Customer($registry);
    $registry->set('customer', $customer);

    // Customer Group
    if ($customer->isLogged()) {
        $config->set('config_customer_group_id', $customer->getGroupId());
    } elseif (isset($session->data['customer']) && isset($session->data['customer']['customer_group_id'])) {
        // For API calls
        $config->set('config_customer_group_id', $session->data['customer']['customer_group_id']);
    } elseif (isset($session->data['guest']) && isset($session->data['guest']['customer_group_id'])) {
        $config->set('config_customer_group_id', $session->data['guest']['customer_group_id']);
    }
}

// Affiliate
if(PACKAGE_AFFILIATE)
    $registry->set('affiliate', new Affiliate($registry));

// Currency
if(PACKAGE_CURRENCY)
    $registry->set('currency', new Currency($registry));

// Tax
if(PACKAGE_TAX)
    $registry->set('tax', new Tax($registry));

// Weight
if(PACKAGE_WEIGHT)
    $registry->set('weight', new Weight($registry));

// Length
if(PACKAGE_LENGTH)
    $registry->set('length', new Length($registry));

// Cart
if(PACKAGE_CART)
    $registry->set('cart', new Cart($registry));

// Encryption
if(PACKAGE_ENCRYPTION)
    $registry->set('encryption', new Encryption($config->get('config_encryption')));

// OpenBay Pro
if(PACKAGE_OPENBAY)
    $registry->set('openbay', new Openbay($registry));

// Event
if(PACKAGE_EVENT) {
    $event = new Event($registry);
    $registry->set('event', $event);

    $loader->model('Event');
    $events = $registry->get('model_event')->getEvent();

    foreach ($events as $result) {
        $event->register($result['trigger'], $result['action']);
    }
}
*/

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


// Maintenance Mode
//$controller->addPreAction(new Action('common/maintenance'));

// SEO URL's
//$controller->addPreAction(new Action('common/seo_url'));

$controller = new Front($registry);
$action = new Action($route);
$controller->dispatch($action, new Action('Error@not_found'));
$response->addHeader('Content-Type', 'text/html; charset=utf-8');
$response->setCompression($config->get('config_compression'));
$response->output();