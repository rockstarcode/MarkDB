<?PHP

namespace MarkDb;

use Carbon\Carbon;
use FilesystemIterator;


class Library {


    protected  $path;
    protected  $parent;
    public $type = 'Library';
    public $size = 0;
    public $modified;
    public $fingerprint;
    public $slug;
    public $name;
    public $attributes = [];
    public $index = [];
    public $libraries = [];
    public $articles = [];
    public $paths = [];
    private  $ignore = ['.','..','.markdb','.idea','composer.json'];

    public function __construct($directory,$parent = null){
        $this->path = $directory;
        $this->parent = $parent;
        $this->name = basename($directory);
        $this->settings();
        $this->fingerprint = $this->slug;

        $iterator =  new FilesystemIterator($directory);

        $this->modified = Carbon::createFromTimestamp($iterator->getMTime());

        foreach ($iterator as $file) {
            $filename = basename($file->getPathname());

            if (!in_array($filename,$this->ignore)){ /// skip . files
                if (!preg_match('/^\./',$filename)){
                    if ($file->isFile()){
                        $object = new Article($file, $this);
                    }
                    else {
                        $object = new Library($file,$this);
                    }
                    $this->attach($object);
                }


            }

        }

    }

    public function parent(){
        return (!empty($this->base) && $this->slug == $this->base) ? false : $this->parent;
    }

    public function attach($object){

        if ($object instanceof Article){
            $this->articles[(string) $object->slug] = $object;
        }

        if ($object instanceof Library){
            $this->libraries[(string) $object->slug] = $object;
        }

        $this->index($object);

    }

    public function index($object){
        $this->index[(string) $object->slug->relativeTo($this->slug)] = $object;
        $this->paths[(string) $object->path] = $object;
        $this->size += $object->size;
        if ($this->parent){
            $this->parent->attach($object);
        }

    }


    public function settings(){
        if (is_file($this->path.'/.markdb')){

            $attributes = yaml_parse_file($this->path.'/.markdb');
            $this->attributes = $attributes;

            foreach($attributes as $key=>$value){
                if (empty($this->{$key})) {
                    $this->{$key} = $value;
                }
            }
        }

        if (!isset($this->slug)){
            $this->slug = $this->name;
        }

        $this->base = empty($this->base) ? ($this->parent() ? $this->parent()->base : false) : $this->base;
        $this->slug = new Slug(MarkDb::sluggify(implode('/',array_filter([($this->parent ? $this->parent->slug : null),$this->slug]))), $this->base);

        if (!empty($this->date)){
            $this->date = \Carbon\Carbon::createFromTimestamp(strtotime($this->date));
        }

    }


    public function get($slug){
        return empty($this->index[$slug]) ? null : $this->index[$slug];
    }

    public function where($conditions = []){
        $found = [];

        foreach($this->index as $index => $object){
            reset($conditions);
            $match = true;
            foreach($conditions as $key=>$value){
                if (empty($object->{$key}) || $object->{$key} !== $value){
                    $match=false;
                }
            }
            if ($match==true){
                $found[$index] = $object;
            }
        }

        return $found;
    }

    public function type($compare = false){
        $class =  get_class($this);
        $split = explode('\\',$class);
        $item = array_pop($split);

        if (!empty($compare)){
            return strtolower(trim($compare)) == strtolower(trim($item));
        }

        return $item;

    }
}
