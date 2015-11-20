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
    public $array_filter;
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

    /**
     * @param Closure $func
     *
     * Sets a function which handles how array collections should be created provided the $items, $page, and $limit for pagination.
     */
    public function setArrayFilter($func){
        $this->array_filter = $func;
    }

    /**
     * @param $items
     * @param $pagination
     * @return array|mixed
     * Handles the conversion of $items into a consumable collection with pagination
     */
    public function collection($items, $pagination){

        if (empty($pagination)){
            $pagination = ['page'=>1,'limit'=>count($items)];
        }

        if (is_callable($this->array_filter)) {
            return call_user_func_array($this->array_filter, [$items, $pagination['page'], $pagination['limit']]);
        }

        return  array_slice($items, (($pagination['page'] - 1) * $pagination['limit']), $pagination['limit']);
    }

    public function parent(){
        return (!empty($this->base) && $this->slug == $this->base) ? false : $this->parent;
    }

    /**
     * @param $object[Article|Library]
     * Insert the object into their placeholders dependent on object type
     */
    public function attach($object){

        if ($object instanceof Article){
            $this->articles[(string) $object->slug] = $object;
        }

        if ($object instanceof Library){
            $this->libraries[(string) $object->slug] = $object;
        }

        $this->index($object);

    }

    /**
     * @param $object[Article|Library]
     *
     * Insert into the index the slug and path of the object for reference by get() or where()
     */

    public function index($object){
        $this->index[(string) $object->slug->relativeTo($this->slug)] = $object;
        $this->paths[(string) $object->path] = $object;
        $this->size += $object->size;
        if ($this->parent){
            $this->parent->attach($object);
        }

    }

    /**
     * Set library settings based on path or existance of .markdb in directory
     */

    public function settings(){
        if (is_file($this->path.'/.markdb')){

            $attributes = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($this->path.'/.markdb'));
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

    /**
     * @param $slug
     * @return (object) Article | Library
     * Return an article or library based ont he slug path defined in the index
     *
     */

    public function get($slug){
        return empty($this->index[$slug]) ? null : $this->index[$slug];
    }

    /**
     * @param array $conditions
     * @return array
     * Search for articles based on article properties declared in YAML
     */
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

    /**
     * @param $item
     * @param $args
     * @return array|mixed|null
     * Allows convertion of index, articles, libraries into paginated array collections
     */
    public function __call($item,$args){
        $pagination= !empty($args) ? current($args) : false;
        if (property_exists($this,$item)){
            return $this->collection($this->{$item},$pagination);
        }
        return null;
    }
}
