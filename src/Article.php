<?PHP

namespace MarkDb;

use Carbon\Carbon;

class Article{
    private $file;
    public  $size;
    public  $name;
    public  $path;
    public  $modified;
    public  $fingerprint;
    public  $slug;
    public  $words;
    protected    $parent;
    private $yaml;
    private $content;

    public function type($compare = false){
        $class =  get_class($this);
        $split = explode('\\',$class);
        $item = array_pop($split);

        if (!empty($compare)){
            return strtolower(trim($compare)) == strtolower(trim($item));
        }

        return $item;
    }

    public function parent(){
        return $this->parent;
    }

    public function __construct($file, $library){
        $this->file = $file;
        $this->parent = $library;
        $this->path =  $file->getPathname();
        $this->name = preg_replace('/\.[^\.]+$/','',basename($this->path));
        $this->size =  $file->getSize();
        $this->modified =  Carbon::createFromTimestamp($file->getMTime());
        $this->fingerprint =  md5($this->path);
        $this->parse();
        $this->settings($this->yaml);
        $this->base = $this->parent->base;

        if (empty($this->slug)){
            $this->slug = preg_replace("/^[\d]+[\_\-]/","",$this->name);
        }
        $this->slug = new Slug(MarkDb::sluggify($this->parent->slug .'/'.$this->slug), $this->base);
        $this->words = str_word_count(strip_tags($this->content()));
    }

    public function read_time(){
        return ceil($this->words / 160);
    }

    public function summary($length_percentage){
        $length = strlen($this->content()) * ($length_percentage/100);
        $input = $this->content();
        if( strlen($input) <= $length )
            return $input;

        $parts = explode(" ", $input);

        while( strlen( implode(" ", $parts) ) > $length )
            array_pop($parts);

        return implode(" ", $parts);
    }


    public function settings($yaml){

        if (!empty($yaml)){

            $attributes = yaml_parse($yaml);

            foreach($attributes as $key=>$value){
                if (empty($this->{$key})) {
                    $this->{$key} = $value;
                }
            }
        }

        if (!empty($this->date)){
            $this->date = \Carbon\Carbon::createFromTimestamp(strtotime($this->date));
        }

    }

    public function parse(){
        $content = file_get_contents($this->path);
        list($delimiter,$yaml,$content) =  array_pad(preg_split('/[\n]*[-]{3}[\n]/', $content, 3),3,'');
        $this->content = trim($content);
        $this->yaml = trim($yaml);
    }

    public function content(){
        return MarkDb::markdown($this->content);
    }

    public function url(){
        return route('post',['slug'=>$this->slug]);
    }
}

