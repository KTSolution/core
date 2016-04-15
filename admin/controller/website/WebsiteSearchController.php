<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 3/13/2016
 * Time: 5:32 PM
 */
class WebsiteSearchController extends Controller
{

    public function index(){
        try{
            $request = $this->request->request;
            $session = $this->session->data;
            $language = $this->language;
            $data = $paging = $sidebar = $filter = $test = $header = $list = array();
            $user = new stdClass(); $user->type = 1;
            $global = __global($user->type);

            $mSetup = $this->model("Page");
            $page_info = $mSetup::where('enable','=',1)
                ->where('id', '=', PAGE_SEARCH)
                ->where('type', '=', PAGE_TYPE_INTERNAL)
                ->first();

            $setup = new stdClass();
            if($page_info){
                $setup->wsTitle             = $language->get_text($page_info->name, $language->current);
                $setup->wsUrl               = $language->get_text($page_info->code, $language->current);
                $setup->wsMetaTitle         = $language->get_text($page_info->title, $language->current);
                $setup->wsMetaDescription   = $language->get_text($page_info->description, $language->current);
                $setup->wsMetaKeyword       = $language->get_text($page_info->keyword, $language->current);
            }


            $sidebar = $this->load->json("common/SideBar");

            // render to js
            $data['test']               = $test;
            $data['header']             = $header;
            $data['filter']             = $filter;
            $data['list']               = $list;
            $data['global']             = $global;
            $data['sidebar']            = $sidebar;
            $data['paging']             = $paging;
            $data['setup']              = $setup;

            return $data;

        } catch (Exception $ex){
            throw $ex;
        }
    }

    public function save() {
        $post = $this->request->post['setup'];
        $language = $this->language;

        $fields = array('code', 'name', 'title', 'description', 'keyword');
        $values = array('wsUrl', 'wsTitle', 'wsMetaTitle', 'wsMetaDescription', 'wsMetaKeyword');
        $json = array('code', 'name', 'title', 'description', 'keyword');

        $merge = array_combine($fields, $values);
        $data = array();
        foreach ($merge as $k => $v) {
            $data[$k] = $post[$v];
        }

        $model = $this->model("Page");
        $model::_save($data, PAGE_HOME, $json, $language);
        
        return $this->index();
    }
}