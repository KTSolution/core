<?php
/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 2/16/2016
 * Time: 3:10 PM
 */

global $routes;
$routes = array(
    PAGE_HOME               => "Home",
    PAGE_ERROR              => "Error",
    PAGE_SEARCH             => "Search",
    PAGE_PRODUCT            => "Product",
    PAGE_ABOUT              => "AboutUs",
    PAGE_BLOG               => "Blog",
    PAGE_SERVICE            => "Service",
    PAGE_CONTACT            => "Contact",
    PAGE_FAQ                => "Faq",
    PAGE_PORTFOLIO          => "Portfolio",
);
define('PAGE_DEFAULT',              $routes[PAGE_HOME]);
define('PAGE_NOT_FOUND',            $routes[PAGE_ERROR]);