<?php
declare(strict_types=1);

namespace test\edwrodrig\temple_core;

use edwrodrig\exception_with_data\ExceptionWithData;
use edwrodrig\temple_core\PharBuilder;
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
        if ( file_exists($this->phar_file))
            unlink($this->phar_file);
        exec(sprintf('rm -rf %s', escapeshellarg($this->output_folder)));
    }

    /**
     * @throws ExceptionWithData
     */
    public function testMakePhar() {

        $builder = new PharBuilder();
        $builder->buildPhar($this->phar_file);

        $this->assertFileExists($this->phar_file);
    }

    /**
     * @throws ExceptionWithData
     */
    public function testMakePharAlreadyExists() {

        touch($this->phar_file);
        $builder = new PharBuilder();
        $builder->buildPhar($this->phar_file);

        $this->assertFileExists($this->phar_file);
    }

    public function testMakePharReadOnly() {

        try {
            ini_set('phar.readonly', '1');

            $builder = new PharBuilder();
            $builder->buildPhar($this->phar_file);
            $this->fail("should throw");

        } catch (ExceptionWithData $exception) {
            $this->assertEquals("can't write a Phar file", $exception->getMessage());
            $this->assertEquals(['output' => $this->phar_file, 'phar.readonly' => '1'], $exception->getData());
        }


    }

}
