<?php


class SearchResult
{


    protected $index = null;


    public $title = null;


    public $snippet = null;


    private $template = "result";


    private $default_template = "result";


    public $url = null;

    
    public $id = null;


    const DEBUG = true;



    public function setTitle($title) {
        $this->title = $title;
    }

    public function setSnippet($snippet) {
        $this->snippet = $snippet;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setTemplate($tpl) {
        $this->template = $tpl;
    }


    public function __construct($title, $snippet = null, $url = null) {
        $this->title = $title;
        $this->snippet = $snippet;
        $this->url = $url;
    }


    /**
     * @method toHtml
     * 
     * Render this search result instance into HTML using the
     * specified template.  Default template is result.tpl.php.
     */
    public function toHtml($params = array()) {

		

        $params = empty($params) ? array() : $params;
        // $props = get_object_vars($this);
        

        $reflection = new \ReflectionClass($this);
        $props = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
    
        // Add public members of this Component instance
        // to the scope so they can be consumed by templates;
        // especially, without `this` keyword.
        foreach($props as $obj) {
            $name = $obj->name;
            $params[$name] = $this->{$name};
        }
        $directory = dirname($reflection->getFileName());

        $path = BASE_PATH . "/modules/sphinx/templates/results/" . $this->template . ".tpl.php";

        if(!is_readable($path)) {
            $path = BASE_PATH . "/modules/sphinx/templates/results/" . $this->default_template . ".tpl.php";
        }
        // print $path;
        if(self::DEBUG === true && !is_readable($path)) {
            throw new \Exception("PATH_RESOLUTION_ERROR: The file does not exist or is not readable: {$path}.");
        }

        extract($params);

        ob_start();
        $found = include($path);
		$content = ob_get_contents();
		ob_end_clean();
    
        return false === $found ? "" : $content;
    }

}