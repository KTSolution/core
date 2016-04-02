<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 2/16/2016
 * Time: 7:33 PM
 */
class BlogController extends Controller
{
    public function index(){
        $data['content_header'] = $this->load->controller('Header');
        $data['content_footer'] = $this->load->controller('Footer');

        if($id = $this->url->getId()){
            $data['content_main'][] = $this->load->controller("Blog@detail", $id);
        } else {
            $data['content_main'][] = $this->load->controller("News@view");
            $data['content_main'][] = $this->load->controller("Blog@view");
        }

        $this->setOutput('master.page.tpl', $data);
    }

    public function view(){

    }
}