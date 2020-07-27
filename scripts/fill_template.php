<?php
declare(strict_types=1);

require_once(__DIR__ . '/../src/TemplateFiller.php');
require_once(__DIR__ . '/../vendor/labo86/exception_with_data/src/ExceptionWithData.php');

use labo86\exception_with_data\ExceptionWithData;
use labo86\temple_core\TemplateFiller;

$usage = sprintf(<<<EOF
Uso : %s -d var_name_1 var_value_1 [-d var_name_2 var_value_2] ... input_dir output_dir
Cada variable a reemplazar se pone con su nombre de variable seguido por su valor.
Se pueden poner tantas variables como se deseen

Variables clásicas:
    tpl_company_tpl
    tpl_project_tpl
    tpl_project_uc_first_tpl
    tpl_project_uc_tpl
EOF, $argv[0]);

$size = count($argv);

if ( $size < 4 ) die($usage);

$input_dir = $argv[$size - 2] ?? die($usage);
$output_dir = $argv[$size - 1] ?? die($usage);

try {
    $replacement_map = TemplateFiller::buildReplacementMapFromCommandLineArgs($argv);
} catch ( ExceptionWithData $exception ) {
    die($usage);
}

try {


    $filler = new TemplateFiller($replacement_map);

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