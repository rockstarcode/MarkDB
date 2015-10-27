<?PHP

namespace MarkDb;

class Slug {
    private $slug;
    private $base;

    public function __construct($slug,$base){
        $this->slug = $slug;
        $this->base = $base;
    }

    public function __toString(){
        return $this->relativeTo($this->base);
    }

    public function relativeTo($base){
        return preg_replace('/^\//','',str_replace($base, '',$this->slug));
    }
}