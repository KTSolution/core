<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 3/13/2016
 * Time: 5:32 PM
 */
class DashboardController extends Controller
{

    public function index(){
        try{
            $request = $this->request->request;
            $session = $this->session->data;
            $data = $paging = $sidebar = $filter = $test = $list = array();


            return $data;

        } catch (Exception $ex){
            throw $ex;
        }
    }
}