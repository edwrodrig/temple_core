<?php
declare(strict_types=1);

namespace test\labo86\temple_core;

use labo86\exception_with_data\ExceptionWithData;
use labo86\temple_core\TemplateFiller;
use Exception;
use PHPUnit\Framework\TestCase;

class TemplateFillerTest extends TestCase
{
    /**
     * @var false|string
     */
    private $path;

    public function setUp() : void {
        $this->path =  tempnam(__DIR__, 'demo_phar');

        unlink($this->path);
        mkdir($this->path, 0777);
    }

    public function tearDown() : void {
        exec('rm -rf ' . $this->path);
    }


    public function replaceBasicProvider()
    {
        return [
            ["hello", "hello"],
            ["project", "tpl_project_tpl"],
            ["company", "tpl_company_tpl"],
            ["Project", "tpl_project_uc_first_tpl"]
        ];
    }

    /**
     * @dataProvider replaceBasicProvider
     * @param $expected
     * @param $actual
     */
    public function testReplaceBasic(string $expected, string $actual)
    {
        $template = new TemplateFiller("company", "project");
        $this->assertEquals($expected, $template->replace($actual));
    }


    /**
     * @throws ExceptionWithData
     */
    public function testFillTemplateInputNoExistent()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("current directory does not exists");
        $path = $this->path;

        $template = new TemplateFiller("company", "project");
        $template->fillTemplate($path . '/input', $path . '/output');

    }

    /**
     * @throws ExceptionWithData
     */
    public function testFillTemplateOutputAlreadyExistent()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("output directory exists");
        $path = $this->path;

        mkdir( $path . '/input');
        mkdir( $path . '/output');

        $template = new TemplateFiller("company", "project");
        $template->fillTemplate($path . '/input', $path . '/output');

    }

    /**
     * @throws Exception
     */
    public function testFillTemplateBasic()
    {
        $path = $this->path;
        mkdir( $path . '/input');
        file_put_contents($path . '/input/tpl_project_tpl', "tpl_company_tpl");

        $template = new TemplateFiller("company", "project");
        $template->fillTemplate($path . '/input', $path . '/output');
        $this->assertFileExists( $path . '/output/project');
        $this->assertEquals("company", file_get_contents($path . '/output/project'));
    }

    public function fillTemplatePathProvider()
    {
        return [
            ["nested/.hidden"],
            [".hidden"],
            ["something"]
        ];
    }

    /**
     * @dataProvider fillTemplatePathProvider
     * @param string $file
     * @throws Exception
     */

    public function testFillTemplatePath(string $file)
    {
        $path = $this->path;
        $dirname = dirname($file);
        mkdir($path . '/input/' . $dirname, 0777, true);

        file_put_contents($path . '/input/' . $file, "content");

        $template = new TemplateFiller("company", "project");
        $template->fillTemplate($path . '/input', $path . '/output');
        $this->assertFileEqualsString( "content", $path . '/output/' . $file);
    }

    /**
     * @throws Exception
     */
    public function testFillTemplateComplex()
    {
        $path = $this->path;
        mkdir( $path . '/input');
        file_put_contents($path . '/input/tpl_project_tpl', "tpl_company_tpl");
        file_put_contents($path . '/input/.hidden', "content");
        mkdir($path . '/input/nested');
        file_put_contents($path . '/input/nested/.hidden', "content");

        $template = new TemplateFiller("company", "project");
        $template->fillTemplate($path . '/input', $path . '/output');
        $this->assertFileEqualsString( "company", $path . '/output/project');
        $this->assertFileEqualsString( "content", $path . '/output/.hidden');
        $this->assertFileEqualsString( "content", $path . '/output/nested/.hidden');
    }

    /**
     * @throws Exception
     */
    public function testFillTemplateIgnore()
    {
        $path = $this->path;
        mkdir( $path . '/input');
        file_put_contents($path . '/input/tpl_project_tpl', "tpl_company_tpl");
        file_put_contents($path . '/input/.hidden', "content");
        mkdir($path . '/input/nested');
        file_put_contents($path . '/input/nested/.hidden', "content");

        $template = new TemplateFiller("company", "project");
        $template->ignore('.hidden');
        $template->fillTemplate($path . '/input', $path . '/output');
        $this->assertFileEqualsString( "company", $path . '/output/project');
        $this->assertFileNotExists( $path . '/output/.hidden');
        $this->assertFileExists( $path . '/output/nested');
        $this->assertFileNotExists( $path . '/output/nested/.hidden');
    }

    public function assertFileEqualsString(string $expected, string $filename, string $message = "") {
        $this->assertFileExists( $filename, $message);
        $this->assertEquals($expected, file_get_contents($filename), $message);
    }


}
