<?php
declare(strict_types=1);

namespace test\edwrodrig\temple_core;

use Exception;
use PHPUnit\Framework\TestCase;

class FillTemplateScriptTest extends TestCase
{

    /**
     * @var false|string
     */
    private $output_folder;

    public function setUp() : void {
        $this->output_folder = tempnam(__DIR__, 'demo_phar');

        unlink($this->output_folder);
    }

    public function tearDown() : void {
        exec(sprintf('rm -rf %s', escapeshellarg($this->output_folder)));
    }

    /**
     * @throws Exception
     */
    public function testScriptOk()
    {
        $command = sprintf('php %s company project %s %s',
            escapeshellarg(__DIR__ . '/../scripts/fill_template.php'),
            escapeshellarg(__DIR__ . '/../src'),
            escapeshellarg($this->output_folder)
        );
        exec($command, $output, $return);
        $this->assertEquals([], $output);
        $this->assertEquals(0, $return);
        $this->assertDirectoryExists($this->output_folder);
    }

    /**
     * @throws Exception
     */
    public function testScriptOkRelative()
    {
        chdir(__DIR__);

        $command = sprintf('php %s company project %s %s',
            escapeshellarg( '../scripts/fill_template.php'),
            escapeshellarg('../src'),
            escapeshellarg(basename($this->output_folder))
        );
        exec($command, $output, $return);
        $this->assertEquals([], $output);
        $this->assertEquals(0, $return);
        $this->assertDirectoryExists($this->output_folder);
    }

    /**
     * @throws Exception
     */
    public function testScriptOneArg()
    {
        $command = sprintf('php %s company', escapeshellarg(__DIR__ . '/../scripts/fill_template.php'));
        exec($command, $output, $return);
        $this->assertStringStartsWith("Uso :", $output[0]);
        $this->assertEquals(0, $return);
        $this->assertDirectoryNotExists($this->output_folder);
    }

    /**
     * @throws Exception
     */
    public function testScriptTwoArg()
    {
        $command = sprintf('php %s company project', escapeshellarg(__DIR__ . '/../scripts/fill_template.php'));
        exec($command, $output, $return);
        $this->assertStringStartsWith("Uso :", $output[0]);
        $this->assertEquals(0, $return);
        $this->assertDirectoryNotExists($this->output_folder);
    }

    /**
     * @throws Exception
     */
    public function testScriptThreeArg()
    {
        $command = sprintf('php %s company project %s',
            escapeshellarg(__DIR__ . '/../scripts/fill_template.php'),
            escapeshellarg(__DIR__ . '/../src')
        );
        exec($command, $output, $return);
        $this->assertStringStartsWith("Uso :", $output[0]);
        $this->assertEquals(0, $return);
        $this->assertDirectoryNotExists($this->output_folder);
    }

    /**
     * @throws Exception
     */
    public function testScriptFourArg()
    {
        $command = sprintf('php %s company project %s %s',
            escapeshellarg(__DIR__ . '/../scripts/fill_template.php'),
            escapeshellarg(__DIR__ . '/no_existent'),
            escapeshellarg($this->output_folder)
        );
        exec($command, $output, $return);
        $this->assertEquals("current directory does not exists", array_shift($output));

        $output = implode("\n",$output);
        $this->assertJson($output);
        $this->assertEquals(1, $return);
        $this->assertDirectoryNotExists($this->output_folder);
    }
}
