<?php
declare(strict_types=1);

require_once(__DIR__ . '/../src/TemplateFiller.php');
require_once(__DIR__ . '/../vendor/labo86/exception_with_data/src/ExceptionWithData.php');

use labo86\exception_with_data\ExceptionWithData;
use labo86\temple_core\TemplateFiller;

$usage = sprintf(<<<EOF
Uso : %s company project input_dir output_dir
Variables disponibles:
    tpl_company_tpl
    tpl_project_tpl
    tpl_project_uc_first_tpl
    tpl_project_uc_tpl
EOF, $argv[0]);

$company = $argv[1] ?? die($usage);
$project = $argv[2] ?? die($usage);

$input_dir = $argv[3] ?? die($usage);
$output_dir = $argv[4] ?? die($usage);


try {
    $filler = new TemplateFiller([
        'tpl_company_tpl' => $company,
        'tpl_project_tpl' => $project,
        'tpl_project_uc_first_tpl' => ucfirst($project),
        'tpl_project_uc_tpl' => strtoupper($project)
    ]);

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