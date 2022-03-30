<?php


class SearchResult
{


    protected $index = null;


    public $title = null;


    public $snippet = null;


    const DEBUG = true;



    public function setTitle($title) {
        $this->title = $title;
    }

    public function setSnippet($snippet) {
        $this->snippet = $snippet;
    }


    public function __construct($title,$snippet = null) {
        $this->title = $title;
        $this->snippet = $snippet;
    }


    /**
     * @method toHtml
     * 
     * Render this search result instance into HTML using the
     * specified template.  Default template is result.tpl.php.
     */
    public function toHtml($template = "result", $params = array()) {

		

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

        $path = $directory . "/../templates/results/" . $template . ".tpl.php";
        $path = BASE_PATH . "/modules/sphinx/templates/results/" . $template . ".tpl.php";
        // print $path;
        if(self::DEBUG === true && !is_readable($path)) {
            throw new \ComponentException("PATH_RESOLUTION_ERROR: The file does not exist or is not readable: {$path}.");
        }

        extract($params);

        ob_start();
        $found = include($path);
		$content = ob_get_contents();
		ob_end_clean();
    
        return false === $found ? "" : $content;
    }

}