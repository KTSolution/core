<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 3/13/2016
 * Time: 5:32 PM
 */
class WebsiteContactController extends Controller
{

    public function index(){
        try{
            $request = $this->request->request;
            $session = $this->session->data;
            $language = $this->language;
            $data = $setting = $paging = $sidebar = $filter = $test = $header = $list = array();
            $user = new stdClass(); $user->type = 1;
            $global = __global($user->type);

            $mSetup = $this->model("Page");
            $page_info = $mSetup::where('enable','=',1)
                ->where('id', '=', PAGE_CONTACT)
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

            $item = array(
                'mIndex'=>0,
                'mLat'=>9.6101651187195,
                'mLng'=>105.95440632788,
                'mSaved'=>true,
                'mTitle'=>"Nam Kỳ Khởi Nghĩa",
                'mTitleOrg'=>'{"vn":"Nam Kỳ Khởi Nghĩa","en":"[Nam Kỳ Khởi Nghĩa]"}',
                'mType'=> 1
            );
            $mList[] = $item;
            $typeList = array(
                MAP_GOOGLE_MARK_SHOPPING => "Shopping",
                MAP_GOOGLE_MARK_RESTAURANT => "Restaurant",
                MAP_GOOGLE_MARK_HOTEL => "Hotel",
            );
            $iconList = array(
                MAP_GOOGLE_MARK_SHOPPING => "Shopping",
                MAP_GOOGLE_MARK_RESTAURANT => "Restaurant",
                MAP_GOOGLE_MARK_HOTEL => "Hotel",
            );

            $setting['map_type'] = MAP_GOOGLE;
            $setting['markerData'] = array(
                'mapLayout' => MAP_GOOGLE_LAYOUT_MAP,
                'list' => $mList,
                'typeList' => $typeList,
                'iconList' => $iconList,
                'zoomLevel' => 17
            );

            // render to js
            $data['test']               = $language;
            $data['header']             = $header;
            $data['filter']             = $filter;
            $data['list']               = $list;
            $data['global']             = $global;
            $data['sidebar']            = $sidebar;
            $data['paging']             = $paging;
            $data['setup']              = $setup;
            $data['setting']            = $setting;

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
        $model::_save($data, PAGE_CONTACT, $json, $language);
        
        return $this->index();
    }

    public function location_save_map(){
        return "";
    }
}