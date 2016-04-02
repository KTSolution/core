<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 2/16/2016
 * Time: 4:03 PM
 */
class ServiceController extends Controller
{

    public function index(){
        $data['content_header'] = $this->load->controller('Header');
        $data['content_footer'] = $this->load->controller('Footer');

        if($id = $this->url->getId()) {
            $data['content_main'] = $this->load->controller('News@detail', $id);
        } else {
            $data['content_main'] = $this->load->controller('News@view');
        }

        $this->setOutPut('master.page.tpl', $data);
    }
}