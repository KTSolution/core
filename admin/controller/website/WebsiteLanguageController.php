<?php
/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 3/13/2016
 * Time: 5:32 PM
 */
class WebsiteLanguageController extends Controller
{

    public function index(){
        try{
            $request = $this->request->request;
            $session = $this->session->data;
            $data = $paging = $sidebar = $filter = $test = $list = $params = $global = array();
            $user = new stdClass(); $user->type = 0;
            $global = __global($user->type);

            // init display
            $fields = array(
                'name'          => array('data' => 'Language', 'icon' => 'true', 'element' => '', 'width' => '50%'),
                'enable'        => array('data' => 'Active', 'icon' => 'false', 'element' => 'checkbox','width' => '25%'),
                'status'        => array('data' => 'Live', 'icon' => 'false', 'element' => 'checkbox','width' => '25%'),
                'code'        => array('data' => 'Code', 'icon' => 'false', 'element' => 'none','width' => '25%'),
            );


            foreach ($request as $key => $value) {
                if ( isset($filter[$key]) ) {
                    $params[$filter[$key]['field']] = $value;
                }

            }

            // init values
            $model = $this->model("Language");
            $list = $model::_filter($params);


            // merge data
            $header = array( 'columns' => $fields, 'rows' => array_keys($fields) );
            $filter = array( 'columns' => $filter, 'rows' => array_keys($filter) );
            $sidebar = $this->load->json("common/SideBar");

            $data['test']               = $test;
            $data['header']             = $header;
            $data['filter']             = $filter;
            $data['list']               = $list;
            $data['global']             = $global;
            $data['paging']             = $paging;
            $data['sidebar']            = $sidebar;


            return $data;

        } catch (Exception $ex){
            throw $ex;
        }
    }

    public function save() {

        $post = $this->request->post;
        $languages = $post['lang'];
        $mLang = $this->model("Language");
        $mLang::_saveLanguageActive($languages);
        if ( count($languages) ) {
            $this->session->data['language'] = LANG_DEFAULT;
        }
        return $this->index();
    }

    public function change_current_lang(){
        $request = $this->request->request;
        $language = $this->language;
        
        if ( isset($request['lang']) && $language->current != $request['lang'] ) {
            $mLang = $this->model("Language");
            $obj = $mLang::_getCurrentLanguageObj($request['lang']);
            $language->change($obj);

            $this->session->data['language'] = $language->current;
        }
    }
}