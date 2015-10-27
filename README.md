## MarkDB - MarkDown Database for Blogs/CMSs

### Installation
```
    composer require "rockstarcode/markdb" 
```

### Usage

```php
$slug = 'path/to/some/article'; 

$MarkDb = new MarkDB\MarkDB('/path/to/files/','base/path/to/form/relative/slugs');

$article = $MarkDb->get($slug);

```

### Laravel

