<?PHP
namespace MarkDb;

use Parsedown;

class MarkDb extends Library {

    protected $directory;

    public $articles = [];
    public $libraries = [];
    public $index;
    public $slug = '';
    protected  $parent = false;

    public function __construct($directory, $base = ''){
        $this->directory = $directory;
        $this->base = $base;
        parent::__construct($directory);
    }

    /**
     * @param $file
     * @return Article|Library
     * Gets a article or library based on path instead of slug
     */

    public function fromFile($file){

        return !empty($this->paths[$file]) ? $this->paths[$file] : null;
    }


    /**
     * Helper function: Non laravel specific dd for die and debug output
     */
    public static function dd()
    {
        array_map(function($x) { var_dump($x); }, func_get_args());
        die;
    }

    /**
     * @param $string
     * @return string
     * Converts strings into slug case
     */

    public static function to_slug($string){
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    }

    /**
     * @param $str
     * @return string
     * creates a slug out of a path without converting / into -
     */
    public static function sluggify($str){
        $explode = explode("/",$str);
        array_walk($explode,function(&$item, $keyi){
            $item = MarkDb::to_slug($item);
        });
        return implode('/',$explode);
    }

    /**
     * @param $text
     * @return string
     * Handles converting the content found in articles from Markdown to HTML
     */

    public static function markdown($text){

        return (new Parsedown())->text($text);
    }


}
