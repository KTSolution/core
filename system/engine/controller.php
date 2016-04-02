<?php
abstract class Controller {
	protected $registry;
	protected $data;

	public function __construct($registry) {
		$this->registry = $registry;
		$this->data = $registry->get('document')->getData();
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	public function controller($name) {
        return $this->load->controller($name);
	}

	public function model($name) {
		return $this->load->eloquent($name);
	}
	
	public function setOutPut($templatePath, $data=array()){
        $this->data = array_merge($this->data, $data);

		$this->response->setOutput($this->load->view($templatePath, $this->data));
	}

	public function render($templatePath, $data=array()){

		$this->data = array_merge($this->data, $data);

		return $this->load->view($templatePath, $this->data);
	}
    
    public function addStyle( $filePath ){
        $file = DIR_STATIC . trim($filePath, '/');
        if ( file_exists( $file ) ) {
            $this->document->addStyle(DIR_STATIC . trim($filePath, '/'));
        } else {
            $this->document->addStyle($filePath);
        }
    }

    public function addScript( $filePath ){
        $file = DIR_STATIC . trim($filePath, '/');
        if ( file_exists( $file ) ) {
            $this->document->addScript(DIR_STATIC . trim($filePath, '/'));
        } else {
            $this->document->addScript($filePath);
        }
    }
}