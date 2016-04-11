<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 3/17/2016
 * Time: 12:51 AM
 */
class SideBarController extends Controller
{
    public function index() {
        try {
            $language = $this->language;
            $isHomePage = $isContactPage =false;
            
            $menu_item = array(
                PAGE_ADMIN_SETTING      => array('data' => 'Setting', 'icon' => 'true',
                    'active' => 'false', 'link' => 'website/setting'),
                PAGE_HOME               => array('data' => 'Home', 'icon' => 'true',
                    'active' => 'false', 'link' => 'website/website-home'),
                PAGE_ERROR              => array('data' => 'Error', 'icon' => 'true',
                    'active' => 'false', 'link' => 'website/website-error'),
                PAGE_SEARCH             => array('data' => 'Search', 'icon' => 'true',
                    'active' => 'false', 'link' => 'website/website-search'),
//                PAGE_PRODUCT            => array('data' => 'Product', 'icon' => 'false',
//                    'active' => 'false', 'link' => 'website/website-product'),
                PAGE_ABOUT              => array('data' => 'About us', 'icon' => 'false',
                    'active' => 'false', 'link' => 'website/website-about'),
//                PAGE_BLOG               => array('data' => 'Blog', 'icon' => 'false',
//                    'active' => 'false', 'link' => 'website/website-blog'),
//                PAGE_SERVICE            => array('data' => 'Service', 'icon' => 'false',
//                    'active' => 'false', 'link' => 'website/website-service'),
                PAGE_CONTACT            => array('data' => 'Contact us', 'icon' => 'false',
                    'active' => 'false', 'link' => 'website/website-contact'),
//                PAGE_FAQ                => array('data' => 'Faq', 'icon' => 'false',
//                    'active' => 'false', 'link' => 'website/website-faq'),
//                PAGE_PORTFOLIO          => array('data' => 'Portfolio', 'icon' => 'false',
//                    'active' => 'false', 'link' => 'website/website-portfolio'),
//                PAGE_ADMIN_BANNER       => array('data' => 'Banner', 'icon' => 'false',
//                    'active' => 'false', 'link' => 'website/website-banner'),
                PAGE_ADMIN_CUSTOM_MENU  => array('data' => 'Custom menu', 'icon' => 'false',
                    'active' => 'false', 'link' => 'website/website-custom-menu'),
                PAGE_ADMIN_EXTRA_PAGE   => array('data' => 'Extra page', 'icon' => 'false',
                    'active' => 'false', 'link' => 'website/website-extra-page'),
                PAGE_ADMIN_LANGUAGE     => array('data' => 'Language', 'icon' => 'false',
                    'active' => 'false', 'link' => 'website/website-language'),
            );

            foreach ($menu_item as &$item) {
                if( $this->url->currentAdmin() == $item['link']){
                    $item['active'] = 'true';
                    $isHomePage = $item['link'] == $menu_item[PAGE_HOME]['link'] ;
                    $isContactPage = $item['link'] == $menu_item[PAGE_CONTACT]['link'] ;
                    break;
                }
            }
            
            $sidebar = new stdClass();
            $mLang = $this->model("Language");
            $lang_item = array(
                    '#type' =>'#select',
                    '#default' => $language->current,
                    '#value' => $mLang::_getLanguageList()
            );
            $lang_item = __render_html($lang_item);


            $sidebar->test          = $language->current;
            $sidebar->menu_item     = $menu_item;
            $sidebar->lang_item     = $lang_item;
            $sidebar->isHomePage    = $isHomePage;
            $sidebar->isContactPage = $isContactPage;

            return $sidebar;
        } catch ( Exception $e ) {
            throw $e;
        }
    }
}