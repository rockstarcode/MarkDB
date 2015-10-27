## MarkDB - MarkDown Database for Blogs/CMSs

### Usage

```php
$slug = 'path/to/some/article'; 

$MarkDb = new MarkDB\MarkDB('/path/to/files/','base/path/to/form/relative/slugs');

$article = $MarkDb->get($slug);

```