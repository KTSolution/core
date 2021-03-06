<?php

define('USER_GUEST',                        -1);
define('USER_SUPER_ADMIN',                  0);
define('USER_ADMIN',                        1);
define('USER_PARTNER',                      2);
define('USER_MEMBER',                       3);

define('USER_STATUS_NONE',                  0);
define('USER_STATUS_LOCK',                  1);


define('ELEMENT_SELECT',                    1);
define('ELEMENT_INPUT',                     2);
define('ELEMENT_CHECKBOX',                  3);
define('ELEMENT_RADIO',                     4);

define('PAGER_LIMIT',                       20);

//Content type
define('CORR_CONTENT_HTML',                 0);
define('CORR_CONTENT_TEXT',                 1);

// mailer
define('ERROR_MAILER_CONNECTION',           2001);
define('ERROR_MAILER_EMAIL',                2002);
define('ERROR_MAILER_DATA',                 2003);
define('ERROR_MAILER_ATTACHMENT',           2004);
define('ERROR_MAILER_SENDING',              2005);
define('ERROR_MAILER_SENDER',               2006);
define('ERROR_MAILER_RECEIVER',             2007);
define('ERROR_MAILER_NONE',                 0);

// PAGE TYPE 
define('PAGE_TYPE_INTERNAL',                 1);
define('PAGE_TYPE_EXTERNAL',                 2);
define('PAGE_TYPE_ADMIN',                    3);

// PAGES
define('PAGE_ADMIN_SETTING',                0);
define('PAGE_ADMIN_BANNER',                 1001);
define('PAGE_ADMIN_LOCATION',               1002);
define('PAGE_ADMIN_LANGUAGE',               1003);
define('PAGE_ADMIN_CUSTOM_MENU',            1004);
define('PAGE_ADMIN_EXTRA_PAGE',             1005);

define('PAGE_HOME',                         1);
define('PAGE_ERROR',                        2);
define('PAGE_SEARCH',                       3);
define('PAGE_PRODUCT',                      4);
define('PAGE_ABOUT',                        5);
define('PAGE_BLOG',                         6);
define('PAGE_SERVICE',                      7);
define('PAGE_CONTACT',                      8);
define('PAGE_FAQ',                          9);
define('PAGE_PORTFOLIO',                    10);

// Table
define('TABLE_PAGE',                        'pages');
define('TABLE_CATEGORY',                    'category');
define('TABLE_NEWS',                        'news');
define('TABLE_LANGUAGE',                    'language');
define('TABLE_SETTING',                     'setting');
define('TABLE_USER',                        'users');
define('TABLE_MEMBER',                      'user');
define('TABLE_TOUR_GUIDE',                  'tour_guide');

// Font
define('FONT_ARIAL',                        'Arial, Helvetica, sans-serif');
define('FONT_DROID',                        'Droid Serif, serif');
define('FONT_LATO',                         'Lato, sans-serif');
define('FONT_LORA',                         'Lora, serif');
define('FONT_OPEN_SANS',                    'Open Sans, sans-serif');
define('FONT_ROBOTO',                       'Roboto, sans-serif');
define('FONT_TOHAMA',                       'Tahoma, Geneva, sans-serif');
define('FONT_UBUNTU',                       'Ubuntu, sans-serif');

define('DISABLE',                           0);
define('ENABLE',                            1);

define('STATUS_UNLOCK',                     0);
define('STATUS_LOCK',                       1);
define('STATUS_LIVE',                       2);

// GOOGLE MAP
define('MAP_STATIC',                        1);
define('MAP_GOOGLE',                        2);

define('MAP_GOOGLE_MARK_SHOPPING',          1);
define('MAP_GOOGLE_MARK_RESTAURANT',        2);
define('MAP_GOOGLE_MARK_HOTEL',             3);

define('MAP_GOOGLE_LAYOUT_MAP',             1);
define('MAP_GOOGLE_LAYOUT_SATELLITE',       2);
define('MAP_GOOGLE_LAYOUT_TERRAIN',         3);