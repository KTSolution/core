<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 3/13/2016
 * Time: 5:32 PM
 */
class MemberController extends Controller
{

    public function index(){
        try{
            $request = $this->request->request;
            $session = $this->session->data;
            $data = $paging = $sidebar = $filter = $test = $list = $params = array();
            $listGender = array();

            // init display
            $fields = array(
                'first_name'        => array('data' => 'First Name', 'icon' => 'true', 'element' => '', 'width' => '15%'),
                'last_name'         => array('data' => 'Last Name', 'icon' => 'true', 'element' => '','width' => '15%'),
                'email'             => array('data' => 'Email', 'icon' => 'true', 'element' => '','width' => '20%'),
                'type'              => array('data' => 'Mod', 'icon' => 'true', 'element' => '', 'width' => '8%'),
                'enable'            => array('data' => 'Active', 'icon' => 'false', 'element' => 'checkbox','width' => '8%'),
            );
            $filter = array(
                'LastName'    => array('data' => "", 'content' => '', 'field' => 'last_name',
                    'label'=>'Last Name','element' => 'text','class'=>'perc20'),
//                'FirstName'    => array('data' => "", 'content' => '', 'field' => 'first_name',
//                    'label'=>'First Name','element' => 'text','class'=>'perc20'),
            );

            foreach ($request as $key => $value) {
                if ( isset($filter[$key]) ) {
                    $params[$filter[$key]['field']] = $value;
                }

            }

            // init values
            $model = $this->model("Member");
            $member = $model::_filter($params);
            $list = $member;
            $totalItems = $member->count();
            

            $listType = array(
                USER_GUEST               =>  "-- Select --",
                USER_SUPER_ADMIN        =>  "Super Admin",
                USER_ADMIN              =>  "Admin",
                USER_PARTNER            =>  "Partner",
                USER_MEMBER             =>  "Member",
            );


//            $header['fields']['first_name']['iconSort'] = 'asc';

            // analysis data
            $paging['PageNum'] = isset($request['pageNum']) ? (int)$request['pageNum'] : 1;
            foreach ($filter as $key => &$value) {
                $value['data'] = isset($request[$key])?$request[$key]:(isset($session[$key])?$session[$key]:$value['data']);
                switch ( $value['element'] ){
                    case 'select':
                        $value['content']   = __render($listType);
                        $value['data']      = __render($value['data'], $listType[$value['data']]);
                        break;
                }
            }
            foreach ($list as &$item) {
                $item['type'] = $listType[$item['type']];
            }


            // merge data
            $header = array( 'columns' => $fields, 'rows' => array_keys($fields) );
            $filter = array( 'columns' => $filter, 'rows' => array_keys($filter) );
            $paging['TotalItem'] = isset($totalItems) ? (int)$totalItems : 0;
            $paging['TotalPage'] = ceil($paging['TotalItem']/ PAGER_LIMIT);
            $paging['PageNext'] = $paging['PageNum'] + 1;
            $paging['PagePrev'] = $paging['PageNum'] - 1;

            $data['test']               = $test;
            $data['header']             = $header;
            $data['filter']             = $filter;
            $data['list']               = $list;
            $data['gender']             = $listType;
            $data['paging']             = $paging;

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