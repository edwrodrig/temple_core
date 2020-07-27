<?php
declare(strict_types=1);

namespace test\labo86\temple_core;

use Exception;
use PHPUnit\Framework\TestCase;

class PharBuilderLocalTest extends TestCase
{

    /**
     * @var false|string
     */
    private string $output_folder;
    private string $phar_file;

    private $path;

    public function setUp(): void
    {
        $this->path = tempnam(__DIR__, 'demo');

        unlink($this->path);
        mkdir($this->path, 0777);

        $this->phar_file = $this->path . '/phar.phar';
        $this->output_folder = $this->path . '/output';
    }

    public function tearDown(): void
    {
        exec('rm -rf ' . $this->path);
    }

    /**
     * @throws Exception
     */
    public function testMakePhar() {
        $script_file = __DIR__ . '/../scripts/make_phar.php';

        $command = sprintf('php -d phar.readonly=Off %s %s', escapeshellarg($script_file), escapeshellarg($this->phar_file));
        exec($command, $output, $return);
        $this->assertEquals([realpath($this->phar_file)], $output);
        $this->assertEquals(0, $return);
        $this->assertFileExists($this->phar_file);
    }

    /**
     * @throws Exception
     */
    public function testRunPhar() {
        $this->testMakePhar();

        $command = sprintf('php %s -d tpl_company_tpl company -d tpl_project_tpl project %s %s', escapeshellarg($this->phar_file), escapeshellarg(__DIR__ . '/../src'), escapeshellarg($this->output_folder));
        exec($command, $output, $return);
        $this->assertEquals([], $output);
        $this->assertEquals(0, $return);
        $this->assertDirectoryExists($this->output_folder);
    }

    /**
     * @throws Exception
     */
    public function testRunPharRelative() {
        $this->testMakePhar();

        chdir($this->path);
        $command = sprintf('php %s -d tpl_company_tpl company -d tpl_project_tpl project %s %s',
            escapeshellarg('phar.phar'),
            escapeshellarg('../../src'),
            escapeshellarg('output')
            );
        exec($command, $output, $return);
        $this->assertEquals([], $output);
        $this->assertEquals(0, $return);
        $this->assertDirectoryExists($this->output_folder);
    }

    /**
     * Error replicado de problemas de is_dir cuando se está dentro de un phar.
     * @throws Exception
     */
    public function testRunPharRelative2() {
        $this->testMakePhar();

        mkdir($this->output_folder);
        chdir($this->output_folder);

        mkdir('test/input', 0777, true);
        touch('test/input/hola');

        chdir('test');
        copy($this->phar_file, 'demo.phar');

        $command = sprintf('php demo.phar -d tpl_company_tpl company -d tpl_project_tpl project %s %s',
            escapeshellarg('input'),
            escapeshellarg('output')
        );
        exec($command, $output, $return);
        $this->assertEquals("", $output[0] ?? "");
        $this->assertEquals(0, $return);
        $this->assertDirectoryExists($this->output_folder . '/test/output');
        $this->assertFileExists($this->output_folder . '/test/output/hola');
    }

}
