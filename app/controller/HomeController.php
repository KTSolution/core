<?php
use tlslib\Tls;
/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 2/16/2016
 * Time: 3:08 PM
 */

class HomeController extends Controller
{
    public function index() {

        $data['content_header'] = $this->load->controller('Header');
        $data['content_footer'] = $this->load->controller('Footer');

        $data['content_main'][] = $this->load->controller("News@view");
        $data['content_main'][] = $this->load->controller("Blog@view");

        $this->setOutPut('master.page.tpl', $data);
    }
}