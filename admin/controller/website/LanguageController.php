<?php
/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 3/13/2016
 * Time: 5:32 PM
 */
class LanguageController extends Controller
{

    public function index(){
        try{
            $request = $this->request->request;
            $session = $this->session->data;
            $data = $paging = $sidebar = $filter = $test = $list = $params = $global = array();
            $user = new stdClass(); $user->type = 0;
            $global = array (
                'isGlobalAdmin'     => in_array($user->type, array(USER_ADMIN)),
                'isMasterAdmin'     => in_array($user->type, array(USER_SUPER_ADMIN, USER_ADMIN)),
                'isPartner'         => in_array($user->type, array(USER_PARTNER, USER_SUPER_ADMIN, USER_ADMIN)),
                'langDefault'       => LANG_DEFAULT,
            );

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
            $model = $this->load->eloquent("Language");
            $list = $model::_filter($params);


            // merge data
            $header = array( 'columns' => $fields, 'rows' => array_keys($fields) );
            $filter = array( 'columns' => $filter, 'rows' => array_keys($filter) );

            $data['test']               = $test;
            $data['header']             = $header;
            $data['filter']             = $filter;
            $data['list']               = $list;
            $data['paging']             = $paging;
            $data['global']             = $global;

            return $data;

        } catch (Exception $ex){
            throw $ex;
        }
    }

    public function edit() {
        $request = $this->request->request;
        $loader = $this->load;

        $pid = $request['pid'];
        $modelMember =$loader->eloquent("Member");
        if( $pid > 0) {
            $items = $modelMember::find($pid);
        } else {
            $items = $modelMember::_make();
        }

        $listType = array(
            USER_GUEST               =>  "-- Select --",
            USER_SUPER_ADMIN        =>  "Super Admin",
            USER_ADMIN              =>  "Admin",
            USER_PARTNER            =>  "Partner",
            USER_MEMBER             =>  "Member",
        );

        $statusList = array(
            USER_STATUS_DEACTIVE    => "Inactive",
            USER_STATUS_ACTIVE      => "Active",
        );

        $data['member']         = $items;
        $data['typeList']       = __render($listType);
        $data['statusList']     = __render($statusList);

        return $data;
    }

    public function save() {
        $loader = $this->load;
        $post = $this->request->post['member'];

        $data['first_name'] = $post['FirstName'];
        $data['last_name'] = $post['LastName'];
        $data['email'] = $post['Email'];
        $data['type'] = $post['Type'];
        $data['enable'] = $post['Active'];
        $data['login_name'] = $post['UserName'];
        if( !empty($post['Password']) ) {
            $data['password'] = $post['Password'];
        }

        $model = $loader->eloquent("Member");
        $data = $model::_save($data, $post['ID']);
//        if ( $post['ID'] > 0) {
//            $member = $post['ID'];
//            $member = $model::_make();
//        } else {
//            unset($post['ID']);
//            $member = $model::create($data);
//        }
//
//        $member->save();
        return $data;
        //return $this->index();
    }

    public function delete() {
        $request = $this->request->request;
        $loader = $this->load;

        $pid = $request['pid'];
        $model = $loader->eloquent('Member');
        $model::find($pid)->delete();

        return $this->index();
    }
}