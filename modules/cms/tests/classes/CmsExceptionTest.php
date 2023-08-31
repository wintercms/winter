<?php

namespace Cms\Tests\Classes;

use System\Tests\Bootstrap\TestCase;
use Cms\Classes\CmsException;
use Cms\Classes\Router;
use Cms\Classes\Theme;
use Winter\Storm\Exception\SystemException;

class CmsExceptionTest extends TestCase
{
    //
    // Tests
    //

    public function testExceptionMask()
    {
        $foreignException = new \Exception('This is a general error');
        $exceptionMask = new SystemException('This is a system exception');
        $exceptionMask->setMask($foreignException);

        $this->assertEquals('This is a general error', $exceptionMask->getMessage());
    }

    public function testCmsExceptionPhp()
    {
        $theme = Theme::load('test');
        $router = new Router($theme);
        $page = $router->findByUrl('/throw-php');

        $error = [
            'file' => 'test.php',
            'line' => 20,
        ];
        $foreignException = new \Symfony\Component\ErrorHandler\Error\FatalError('This is a general error', 100, $error);
        $this->setProtectedProperty($foreignException, 'file', "/modules/cms/classes/CodeParser.php(165) : eval()'d code line 7");

        $exception = new CmsException($page, 300);
        $exception->setMask($foreignException);

        $this->assertEquals($page->getFilePath(), $exception->getFile());
        $this->assertEquals('PHP Content', $exception->getErrorType());
        $this->assertEquals('This is a general error', $exception->getMessage());
    }
}
