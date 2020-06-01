<?php
declare(strict_types=1);

namespace test\edwrodrig\temple_core;

use Exception;
use PHPUnit\Framework\TestCase;

class PharBuilderTest extends TestCase
{

    /**
     * @var false|string
     */
    private string $output_folder;
    private string $phar_file;

    public function setUp() : void {
        $this->output_folder = tempnam(__DIR__, 'demo_phar');
        $this->phar_file = $this->output_folder . '.phar';

        unlink($this->output_folder);
    }

    public function tearDown() : void {
        unlink($this->phar_file);
        exec(sprintf('rm -rf %s', escapeshellarg($this->output_folder)));
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

        $command = sprintf('php %s company project %s %s', escapeshellarg($this->phar_file), escapeshellarg(__DIR__ . '/../src'), escapeshellarg($this->output_folder));
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

        chdir(__DIR__);
        $command = sprintf('php %s company project %s %s',
            escapeshellarg(basename($this->phar_file)),
            escapeshellarg('../src'),
            escapeshellarg(basename($this->output_folder))
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

        $command = sprintf('php demo.phar company project %s %s',
            escapeshellarg('input'),
            escapeshellarg('output')
        );
        exec($command, $output, $return);
        $this->assertEquals("", $output[0] ?? "");
        $this->assertEquals(0, $return);
        $this->assertDirectoryExists($this->output_folder);
    }

}
