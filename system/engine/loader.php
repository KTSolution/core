<?php
final class Loader {
	private $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function json($route, $data = array()) {
		if (!is_bool(strpos($route, "@"))){
			$method = substr($route, strpos($route, "@")+strlen("@"));
			$route =  substr($route, 0, strpos($route, "@"));
		}

		$parts = explode('/', str_replace('../', '', (string)$route));

		while ($parts) {
			$curr = array_pop($parts);
			$class = preg_replace('/[^a-zA-Z0-9]/', '', ucfirst($curr)) . 'Controller';
			//$class = preg_replace('/[^a-zA-Z0-9]/', '', implode('/', $parts)) . 'Controller';

			$file = DIR_APP . 'controller/' . implode('/', $parts) . "/" . $class . '.php';

			if (is_file($file)) {
				include_once($file);
				break;
			} else {
				array_push($data, $curr);
			}
		}

		$controller = new $class($this->registry);

		if (!isset($method)) {
			$method = 'index';
		}

		// Stop any magical methods being called
		if (substr($method, 0, 2) == '__') {
			return false;
		}

		$output = '';
		if (is_callable(array($controller, $method))) {
			$output = call_user_func(array($controller, $method), $data);
		}

		// $this->event->trigger('post.controller.' . $route, $output);

		return $output;
	}

	public function controller($route, $data = array()) {
        if (!is_bool(strpos($route, "@"))){
            $method = substr($route, strpos($route, "@")+strlen("@"));
			$route =  substr($route, 0, strpos($route, "@"));
        }

		$parts = explode('/', str_replace('../', '', (string)$route));

		// Break apart the route
		while ($parts) {
			$class = preg_replace('/[^a-zA-Z0-9]/', '', implode('/', $parts)) . 'Controller';
			$file = DIR_APP . 'controller/' . $class . '.php';

			if (is_file($file)) {
				include_once($file);
				break;
			} else {
				array_push($data, array_pop($parts));
			}
		}

		$controller = new $class($this->registry);

		if (!isset($method)) {
			$method = 'index';
		}

		// Stop any magical methods being called
		if (substr($method, 0, 2) == '__') {
			return false;
		}

		$output = '';
		if (is_callable(array($controller, $method))) {
			$output = call_user_func(array($controller, $method), $data);
		}

		// $this->event->trigger('post.controller.' . $route, $output);

		return $output;
	}

	public function model($model, $method="") {
		$file = DIR_APP . 'model/' . ucfirst($model) . 'Model' . '.php';
		$class = preg_replace('/[^a-zA-Z0-9]/', '', ucfirst($model)) . 'Model';

		if (file_exists($file)) {
			include_once($file);

			$return = new $class($this->registry);

			if(!empty($method)){

				return $return->$method();
			}

			return $return;
		} else {
			//trigger_error('Error: Could not load model ' . $file . '!');
			//exit();
			return null;
		}

		// $this->event->trigger('post.model.' . str_replace('/', '.', (string)$model), $output);
	}
	
	public function eloquent($model) {
		$file = DIR_APP . 'model/' . ucfirst($model) . 'Model' . '.php';
		$class = preg_replace('/[^a-zA-Z0-9]/', '', ucfirst($model)) . 'Model';

		if (file_exists($file)) {
			include_once($file);

			return $class;
		} else {
			trigger_error('Error: Could not load model ' . $file . '!');
			exit();
		}

		// $this->event->trigger('post.model.' . str_replace('/', '.', (string)$model), $output);
	}

	public function view($template, $data = array()) {
		//$file = DIR_TEMPLATE . $template;
		$file = DIR_VIEW . $template;

		if (file_exists($file)) {
			extract($data);

			ob_start();

			require($file);

			$output = ob_get_contents();

			ob_end_clean();
		} else {
			trigger_error('Error: Could not load template ' . $file . '!');
			exit();
		}

		return $output;
	}

	public function template($template) {
		//$file = DIR_TEMPLATE . $template;
		$file = DIR_VIEW . $template;

		if (file_exists($file)) {
			$tpl = new TemplatePower($file);

			return $tpl;
		} else {
			trigger_error('Error: Could not load template ' . $file . '!');
			exit();
		}
	}

//    public function smarty($template) {
//        // $this->event->trigger('pre.view.' . str_replace('/', '.', $template), $data);
//
//        $file = DIR_TEMPLATE . $template;
//
//        if (file_exists($file)) {
//            $tpl = new TemplatePower($file);
//
//            return $tpl;
//        } else {
//            trigger_error('Error: Could not load template ' . $file . '!');
//            exit();
//        }
//
//        // $this->event->trigger('post.view.' . str_replace('/', '.', $template), $output);
//
//        return $tpl->printToScreen();
//    }

	public function file($filename) {
		$_ = array();
		$file = DIR_CONFIG . $filename . '.php';

		if (!file_exists($file)) {
			$file = DIR_APP_CONFIG . $filename . '.php';
		}

		if (file_exists($file)) {
			$_ = require($file);
		}
		
		return $_;
	}
	
	public function helper($helper) {
		$file = DIR_SYSTEM . 'helper/' . str_replace('../', '', (string)$helper) . '.php';

		if (file_exists($file)) {
			include_once($file);
		} else {
			trigger_error('Error: Could not load helper ' . $file . '!');
			exit();
		}
	}

	public function config($config) {
		$this->registry->get('config')->load($config);
	}

	public function language($language) {
		return $this->registry->get('language')->load($language);
	}

}
