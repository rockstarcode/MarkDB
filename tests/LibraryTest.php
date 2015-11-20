<?php
include('BaseTester.php');
class LibraryTest extends BaseTester
{
    public function testCanCreateLibrary()
    {
        $markdb = new MarkDb\MarkDb('./tests/test_library',dirname(__DIR__));
        $lib1 = new MarkDb\MarkDb('./tests/test_library/dir1',dirname(__DIR__));
        $lib2 = new MarkDb\MarkDb('./tests/test_library/dir2',dirname(__DIR__));

        $this->assertEquals(count($markdb->libraries), 5);
        $this->assertEquals(count($lib1->articles), 3);
        $this->assertEquals(count($lib2->articles), 1);
        $this->assertEquals(count($markdb->articles), 5);
        $this->assertEquals(count($markdb->index), 10);

    }

    public function testCanCreateDeepLibrary()
    {

        $lib1 = new MarkDb\MarkDb('./tests/test_library/dir1/dir2/dir3',dirname(__DIR__));
        $this->assertEquals(count($lib1->articles), 1);

    }

    public function testArticlesFound(){

        $markdb = new MarkDb\MarkDb('./tests/test_library',dirname(__DIR__));
        $lib1 = new MarkDb\MarkDb('./tests/test_library/dir1',dirname(__DIR__));
        $lib2 = new MarkDb\MarkDb('./tests/test_library/dir2',dirname(__DIR__));
        $this->assertEquals(count($lib1->articles), 3);
        $this->assertEquals(count($lib2->articles), 1);
        $this->assertEquals(count($markdb->articles), 5);

    }


    public function testArticleContent(){

        $markdb = new MarkDb\MarkDb('./tests/test_library/dir2');

        $article = current($markdb->articles);

        $this->assertEquals($article->title, 'Article Title Dir 2');
        $this->assertEquals($article->author, 'Author Name');
        $this->assertEquals($article->content(), '<h3>Hello World</h3>
<p>This is my article in Dir 2</p>');

    }


    public function testSlugCreation()
    {
        $markdb = new MarkDb\MarkDb('./tests/test_library');
        $this->assertTrue($markdb->index['dir1/dir2/dir3/article-with-snakecase'] instanceOf MarkDb\Article);
        $this->assertTrue($markdb->index['dir2/article'] instanceOf MarkDb\Article);
        $this->assertTrue($markdb->index['dir2/article'] instanceOf MarkDb\Article);
        $this->assertTrue($markdb->index['dir1/my-custom-slug'] instanceOf MarkDb\Article);
        $this->assertTrue($markdb->index['dir1/dirb'] instanceOf MarkDb\Library);

    }

    public function testArticleSettings()
    {
        $markdb = new MarkDb\MarkDb('./tests/test_library/dir2');

        $article = current($markdb->articles);
        $this->assertEquals($article->authdeck,'author.name');

    }

}
?>