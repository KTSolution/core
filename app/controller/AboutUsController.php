<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 2/17/2016
 * Time: 12:31 AM
 */
class AboutUsController extends Controller
{
    public function index(){
        $data['content_header'] = $this->load->controller('Header');
        $data['content_footer'] = $this->load->controller('Footer');
        $dataNews = $this->load->controller("News@view");
        $dataBlog = $this->load->controller("Blog@view");
        $data['content_main'][] = $dataNews;
        $data['content_main'][] = $dataBlog;

        $this->setOutput('master.page.tpl', $data);
    }
}