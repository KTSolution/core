<?php
class Document {
	private $domain = "";
	private $language = array("code"=>"", "direction"=>"");
	private $title = "";
	private $description = "";
	private $keywords = "";
	private $links = array();
	private $styles = array();
	private $scripts = array();

    public function setLanguage($language) {
        $this->language = $language;
    }

    public function getLanguge() {
        return $this->language;
    }

    public function setBase($domain) {
        $this->domain = $domain;
    }

    public function getBase() {
        return $this->domain;
    }

    public function setTitle($title) {
		$this->title = $title;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}

	public function getKeywords() {
		return $this->keywords;
	}

	public function addLink($href, $rel) {
		$this->links[$href] = array(
			'href' => $href,
			'rel'  => $rel
		);
	}
//$this->styles[$css['name']]="<link href=\"{$css['href']}\" rel=\"{$css['rel']}\" type=\"text/css\" media=\"{$css['media']}\">\n";
	public function getLinks() {
		return $this->links;
	}

	public function addStyle($data, $type="file") {
        if( !isset($data['name']) ) $data['name'] = $data['data'];
        if( !isset($data['rel']) ) $data['rel'] = "stylesheet";
        if( !isset($data['media']) ) $data['media'] = "media";
        if( !isset($data['type']) ) $data['type'] = $type;

		$this->styles[$data['name']] = $data;
	}

	public function getStyles() {
        $css = "";
        foreach ($this->styles as $style) {
            $css .= "<link href=\"{$style['data']}\" rel=\"{$style['rel']}\" type=\"text/css\" media=\"{$style['media']}\">\n";
        }
		return $css;
	}

	public function addScript($data, $kind="text/javascript") {
        if( is_array($data)) {
            if( !isset($data['name']) ) $data['name'] = $data['href'];
            if( !isset($data['kind']) ) $data['kind'] = $kind;
        } else if ( is_string($data)){
            $data = array('name' => $data);
            $data['data'] = $data['name'];
            $data['kind'] = $kind;
            $data['type'] = "file";
        }

		$this->scripts[$data['name']] = $data;
	}

	public function getScripts($type = "file") {
        $js = "";

        foreach ($this->scripts as $script) {
            if ( $type == $script['type']) {
                if( $type == "inline") {
                    $js .= "<script type=\"{$script['kind']}\">\n";
                    $js .= "<!--//--><![CDATA[//><!--\n";
                    $js .= "var {$script['name']} = " . json_encode($script['data']) . ";\n";
                    $js .= "//--><!]]>\n";
                    $js .= "</script>\n";
                } else {
                    $js .= "<script src=\"{$script['data']}\" type=\"{$script['kind']}\"></script>\n";
                }
            }
        }

        return $js;
	}

	public function getData() {
		$data['direction']      = $this->language['direction'];
		$data['lang']           = $this->language['code'];
		$data['base']           = $this->domain;
		$data['title']          = $this->title;
		$data['description']    = $this->description;
		$data['keywords']       = $this->keywords;
		$data['links']          = $this->getLinks();
		$data['styles']         = $this->getStyles();
		$data['scripts']        = $this->getScripts();
		$data['inline_scripts'] = $this->getScripts("inline");
		//$data['inline_scripts'] = $this->getScripts("inline");

        return $data;
	}
}
