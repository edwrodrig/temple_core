<?php
declare(strict_types=1);

use labo86\exception_with_data\ExceptionWithData;
use labo86\temple_core\TemplateFiller;

require_once(__DIR__ . '/../src/TemplateFiller.php');
require_once(__DIR__ . '/../vendor/labo86/exception_with_data/src/ExceptionWithData.php');

$usage = sprintf("Uso : %s company project input_dir output_dir", $argv[0]);

$company = $argv[1] ?? die($usage);
$project = $argv[2] ?? die($usage);

$input_dir = $argv[3] ?? die($usage);
$output_dir = $argv[4] ?? die($usage);


try {
    $filler = new TemplateFiller($company, $project);

    //ignoramos los archivos o carpetas con nombre .git
    $filler->ignore('.git');

    //construimos template
    $filler->fillTemplate($input_dir, $output_dir);

} catch ( ExceptionWithData $exception ) {
    echo $exception->getMessage() , "\n";
    echo json_encode([ 'd' => $exception->getData(), 't' => $exception->getTrace()], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit(1);

} catch ( Throwable $exception ) {
    echo $exception->getMessage() , "\n";
    echo json_encode([ 't' => $exception->getTrace()], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit(1);
}