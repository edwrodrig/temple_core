<?php
declare(strict_types=1);

use edwrodrig\temple_core\TemplateFiller;

require_once(__DIR__ . '/../src/TemplateFiller.php');

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

} catch ( Throwable $exception ) {
    echo $exception->getMessage() , "\n";
    exit(1);
}