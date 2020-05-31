<?php
declare(strict_types=1);

namespace test\edwrodrig\temple_core;

use edwrodrig\temple_core\TemplateFiller;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class TemplateFillerTest extends TestCase
{

    private vfsStreamDirectory $root;

    public function setUp(): void
    {
        $this->root = vfsStream::setup();
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


    public function testFillTemplateInputNoExistent()
    {
        $path = $this->root->url();

        $template = new TemplateFiller("company", "project");
        $this->assertFalse($template->fillTemplate($path . '/input', $path . '/output'));

    }

    public function testFillTemplateOutputAlreadyExistent()
    {
        $path = $this->root->url();
        mkdir( $path . '/input');
        mkdir( $path . '/output');

        $template = new TemplateFiller("company", "project");
        $this->assertFalse($template->fillTemplate($path . '/input', $path . '/output'));
    }

    public function testFillTemplateBasic()
    {
        $path = $this->root->url();
        mkdir( $path . '/input');
        file_put_contents($path . '/input/tpl_project_tpl', "tpl_company_tpl");

        $template = new TemplateFiller("company", "project");
        $this->assertTrue($template->fillTemplate($path . '/input', $path . '/output'));
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
     */

    public function testFillTemplatePath(string $file)
    {
        $path = $this->root->url();
        $dirname = dirname($file);
        mkdir($path . '/input/' . $dirname, 0777, true);

        file_put_contents($path . '/input/' . $file, "content");

        $template = new TemplateFiller("company", "project");
        $this->assertTrue($template->fillTemplate($path . '/input', $path . '/output'));
        $this->assertFileEqualsString( "content", $path . '/output/' . $file);
    }

    public function testFillTemplateComplex()
    {
        $path = $this->root->url();
        mkdir( $path . '/input');
        file_put_contents($path . '/input/tpl_project_tpl', "tpl_company_tpl");
        file_put_contents($path . '/input/.hidden', "content");
        mkdir($path . '/input/nested');
        file_put_contents($path . '/input/nested/.hidden', "content");

        $template = new TemplateFiller("company", "project");
        $this->assertTrue($template->fillTemplate($path . '/input', $path . '/output'));
        $this->assertFileEqualsString( "company", $path . '/output/project');
        $this->assertFileEqualsString( "content", $path . '/output/.hidden');
        $this->assertFileEqualsString( "content", $path . '/output/nested/.hidden');
    }

    public function testFillTemplateIgnore()
    {
        $path = $this->root->url();
        mkdir( $path . '/input');
        file_put_contents($path . '/input/tpl_project_tpl', "tpl_company_tpl");
        file_put_contents($path . '/input/.hidden', "content");
        mkdir($path . '/input/nested');
        file_put_contents($path . '/input/nested/.hidden', "content");

        $template = new TemplateFiller("company", "project");
        $template->ignore('.hidden');
        $this->assertTrue($template->fillTemplate($path . '/input', $path . '/output'));
        $this->assertFileEqualsString( "company", $path . '/output/project');
        $this->assertFileNotExists( $path . '/output/.hidden');
        $this->assertFileNotExists( $path . '/output/nested');
        $this->assertFileNotExists( $path . '/output/nested/.hidden');
    }

    public function assertFileEqualsString(string $expected, string $filename, string $message = "") {
        $this->assertFileExists( $filename, $message);
        $this->assertEquals($expected, file_get_contents($filename), $message);
    }


}
