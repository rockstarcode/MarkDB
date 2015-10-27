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

    public function fromFile($file){

        return !empty($this->paths[$file]) ? $this->paths[$file] : null;
    }


    public static function dd()
    {
        array_map(function($x) { var_dump($x); }, func_get_args());
        die;
    }

    public static function to_slug($string){
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    }

    public static function sluggify($str){
        $explode = explode("/",$str);
        array_walk($explode,function(&$item, $keyi){
            $item = MarkDb::to_slug($item);
        });
        return implode('/',$explode);
    }

    public static function markdown($text){

        return (new Parsedown())->text($text);
    }


}
