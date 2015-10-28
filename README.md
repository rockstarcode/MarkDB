## MarkDB - MarkDown Database for Blogs/CMSs

MarkDB is an engine that allows you to use MarkDown + Yaml files as data points for your CMS or Blog. 

The intent is allow you to separate content creation from the platform which controls your application design. 
This allows you to write your blog or project in any method you'd like and attach content as if it were database driven. 

This project stemmed from my blog at [RockstarCode](http://www.rockstarcode.com) where I didnt want to have to design my site via a CMS like OctoberCMS or Wordpress
which would force me to write posts via their interface, nor did I want to store content in a database which needed to be sync'd. 

The solution I chose was to create a flat file .git repo to house my articles and MarkDB was the interface to collect and manage posts. 

This allows me to write posts and preview them locally before pushing them to different environments by simple git push. 

## Features

* Pretty URL slug to identify files or directories
* Customize settings per file/directory 
* Use a git repo to version control your data points
* Simple search for articles via attributes 
* [FUTURE] - caching 

### Libraries vs Articles

MarkDB will look for files and directories given a $base path 

From there it will provide a list of Libraries (directories) and Articles (Files)

Libraries hold collections of child libraries and all child articles to allow you to loop thru if needed to create navigation. 
For example you may have a base blog with navigation on various topics [Rants, Tutorials, Personal Thoughts] 
Your base new MarkDB('/path/to/blog/data') would house 3 libraries Rants, Tutorials, Personal Thoughts
which you can use to pull unique data from or link to 

Articles house file information in both YAML & MarkDown which you can use to customize data and presentation. 
```markdown
    ---
    title: Article Title
    author: Authr Name
    author_email: Author@Email.com
    authdeck: author.name
    ---
    ### Hello World
    
    This is my article
```

Everything between ```---``` will be processed as yaml, below the second ```---``` will be processed as markdown


### Requirements
* LibYaml & pecl yaml for PHP
* Carbon for data time 

### Installation
```
    composer require "rockstarcode/markdb:dev-master"
```


### Usage

```php
$MarkDb = new MarkDB\MarkDB('/path/to/files/','base/path/to/form/relative/slugs');

$slug = 'path/to/some/article'; 

$article = $MarkDb->get($slug); # (object) Article 
   
   $article->slug           # url friendly path that identifies article in MarkDB
   $article->{$property}    # properties extracted from YAML processing of the article
   $article->content()      # processed MarkDown of content of article
   $article->read_time()    # helper to calculate words in article and average read time
   $article->summary()      # Extract certain percentage of content for preview 

$find = $MarkDb->where(['author'=>'Author Name','category'=>'Books Ive Read']);  (Array)[Articles]

foreach($MarkDb->libraries as $library){
     $library->articles     # list of articles within library
     $library->libraries    # list of child libraries
     $library->index        # list of all libraries/articles by slug
     $library->slug         # slug which identifies library
     
}
```

### Laravel

MarkDB comes with a Laravel 5.* Service Provider which will add MarkDB as a facade to your application

```php
# .env
MARKDB_PATH=/path/to/blog-cms

# config/app.php
    
    'providers'=>[
        ...
        \MarkDB\Support\Laravel\MarkDBServiceProvider::class,
    ],
    
    'aliases' => [
        ...
        'MarkDB' => \MarkDB\Support\Laravel\MarkDBFacade::class,
    ]
```
a sample route to see articles in Laravel : 

```php
#routes.php
  
  Route::get('/posts/{slug}', ['as'=>'post','uses'=>function($slug){
        
        $markdb = app()->make("markdb");
        
        $article = $markdb->get($slug);
         
        return view('post',['article'=>$article]); 
         
  }])->where('slug','(.*)');
```

