<?php
declare(strict_types=1);

namespace labo86\temple_core;

use ArrayIterator;
use labo86\exception_with_data\ExceptionWithData;
use Phar;
use Throwable;

class PharBuilder
{
    private array $files = [
        'scripts/fill_template.php' => __DIR__ . '/../scripts/fill_template.php',
        'src/TemplateFiller.php' => __DIR__ . '/TemplateFiller.php',
        'vendor/labo86/exception_with_data/src/ExceptionWithData.php' => __DIR__ . '/../vendor/labo86/exception_with_data/src/ExceptionWithData.php'
    ];

    /**
     * Este método construye un Phar
     * @param string $output
     * @return bool
     * @throws ExceptionWithData
     */
    public function buildPhar(string $output) {
        if ( !Phar::canWrite() )
            throw new ExceptionWithData("can't write a Phar file", ['output' => $output, 'phar.readonly' => ini_get('phar.readonly')]);
        if ( file_exists($output) )
            unlink($output);
        $phar = new Phar($output, 0, 'temple_core.phar');
        $phar->convertToExecutable(Phar::TAR, Phar::GZ);
        $phar->startBuffering();
        $phar->buildFromIterator(new ArrayIterator($this->files));
        $stub = $phar->createDefaultStub('scripts/fill_template.php');
        $phar->setStub( $stub );
        $phar->stopBuffering();
        return true;
    }

    /**
     * El script que contenga esta llamada debe tener configurada la variable {@see https://www.php.net/manual/es/phar.configuration.php#ini.phar.readonly phar.readonly} en <strong>On</strong>
     * Eso se puede hacer modificando el archivo {@see https://www.php.net/manual/en/configuration.file.php php.ini} o llamando el script con <code>php -d phar.readonly=Off</code>.
     * El primer argumento que captura es el nombre de phar de salida.
     */
    public static function consoleLaunch() {
        global $argv;
        $output = $argv[1] ?? __DIR__ . '/../temple_core.phar';

        try {
            $builder = new PharBuilder();
            $builder->buildPhar($output);
            printf(realpath($output));
            return 0;
        } catch ( Throwable $exception ) {
            printf($exception->getMessage());
            return 1;
        }
    }
}