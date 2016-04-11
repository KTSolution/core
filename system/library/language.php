<?php
class Language {
	private $default = LANG_DEFAULT;
	public $current = LANG_DEFAULT;
	private $directory = "english";
	public $data = array();

	public function __construct($directory = '') {
		$this->directory = $directory;
	}

	public function change( $lang) {
		if( isset($lang->code)) {
			$this->current = $lang->code;	
		}

		if( isset($lang->directory)) {
			$this->directory = $lang->directory;
		}
	}
	
	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : $key);
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
	}

	public function all() {
		return $this->data;
	}
	
	public function load($filename) {
		$_ = array();

		$file = DIR_LANGUAGE . $this->default . '/' . $filename . '.php';

		if (file_exists($file)) {
			require($file);
		}

		$file = DIR_LANGUAGE . $this->directory . '/' . $filename . '.php';

		if (file_exists($file)) {
			require($file);
		}

		$this->data = array_merge($this->data, $_);

		return $this->data;
	}

	public function set_text($old_content, $new_content, $lang) {
		$old_content = json_decode($old_content);
		if(!is_object($old_content)) {
			$old_content = new stdClass();
		}
		$old_content->$lang = trim($new_content);
		return json_encode($old_content);
	}

	public function get_text($content, $lang) {
		$obj = json_decode($content);
		if( is_object($obj) ) {
			if ( isset($obj->$lang) ) {
				return trim($obj->$lang);
			}

            $content = isset($obj->{LANG_DEFAULT}) ? '[' . trim($obj->{LANG_DEFAULT}) . ']' : "";
			return $content;
		}
		return $content;
	}
}