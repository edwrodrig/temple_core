<?php
declare(strict_types=1);

namespace edwrodrig\temple_core;

use DirectoryIterator;
use Exception;
use Generator;

class TemplateFiller
{
    public string $company;
    public string $project;

    public array $ignored_file_list = [];

    /**
     * Esta clase sirve para llenar un directorio template.
     * <h2>Modo de uso</h2>
     * <code>
     * $filler = new TemplateFiller('edwrodrig', 'project');
     *
     * //ignoramos los archivos o carpetas con nombre .git
     * $filler->ignore('.git');
     *
     * //construimos template
     * $filler->fillTemplate('input_dir', 'output_dir');
     * </code>
     * @see fillTemplate() para saber como se transforma el directorio
     * @see replace() para saber como se transforman los nombres y los contenidos de los archivos
     * @param string $company
     * @param string $project
     */
    public function __construct(string $company, string $project) {
        $this->company = $company;
        $this->project = $project;
    }

    /**
     * Registra nombres de archivos a ignorar por {@see filesToReplace()}.
     * @param string ...$files un nombre de archivo sin ruta
     */
    public function ignore(string ...$files) {
        foreach ( $files as $file)
            $this->ignored_file_list[] = $file;
    }

    /**
     * Transforma un string de un template.
     * Este método transforma las ocurrencias de las variables de template por sus correspondientes versiones.
     * Las variables reconocidas son:
     *  - tpl_company_tpl
     *  - tpl_project_tpl
     *  - tpl_project_uc_first_tpl
     *  - tpl_project_uc_tpl
     *
     * Se eligió esa notación <code>tpl_algo_tpl</code> para que las variables sean compatibles con {@see https://www.php-fig.org/psr/psr-4/ PSR-4}
     * @param string $string el string a reemplazar
     * @return string el string reemplazado
     */
    public function replace(string $string) : string {
        $string = str_replace("tpl_company_tpl", $this->company, $string);
        $string = str_replace("tpl_project_tpl", $this->project, $string);
        $string = str_replace("tpl_project_uc_first_tpl", ucfirst($this->project), $string);
        $string = str_replace("tpl_project_uc_tpl", strtoupper($this->project), $string);
        return $string;
    }

    /**
     * Esta función recorre los archivos de un directorio excluyendo los siguientes archivos:
     *  - .
     *  - ..
     *  - los nombres de archivos registrador con {@see ignore()}
     *
     * La llave de cada arreglo es el nombre del archivo original y el valor es nombre transformado por {@see replace()}.
     * <strong>Ambos excluyen su ruta</strong>
     * @param string $path la ruta del directorio que tiene los templates
     * @return Generator|string[]
     */
    public function filesToReplace(string $path) {
        foreach ( new DirectoryIterator($path) as $file_info ) {
            $absolute_filename = $file_info->getPathname();
            $output_filename = $file_info->getBasename();
            if ( $file_info->isDot() ) continue;
            if ( in_array($output_filename, $this->ignored_file_list) ) continue;
            $output_filename = $this->replace($output_filename);
            yield $absolute_filename => $output_filename;
        }
    }

    /**
     * Esta función crea una copia de un directorio que tiene las siguientes diferencias:
     * - con nombres de archivos transformados por {@see replace()}
     * - con contenido reemplazado por {@see replace()}
     * - se excluyen los archivos registrados por {@see ignore()}
     *
     * @param string $current_directory el directorio original con los templates
     * @param string $output_directory el directorio de salida con los templates llenados
     * @throws Exception
     * @api
     */
    public function fillTemplate(string $current_directory, string $output_directory) {
        if ( !$this->isDirForPhar($current_directory) )
            throw new Exception("current directory does not exists ");
        if ( file_exists($output_directory) )
            throw new Exception("output directory exists");
        mkdir($output_directory, 0777, true);

        foreach ($this->filesToReplace($current_directory) as $absolute_filename => $output_filename) {
            if ( is_file($absolute_filename) ) {
                $contents = file_get_contents($absolute_filename);
                $contents = $this->replace($contents);
                file_put_contents($output_directory . "/" . $output_filename, $contents);

            } else if ( is_dir($absolute_filename) ) {
                $this->fillTemplate($absolute_filename, $output_directory . "/" . $output_filename);

            }
        }
    }

    /**
     * Esta función resuelve el problema de usar {@see is_dir()} dentro de un Phar con rutas relativas.
     * Cuando se usa solamente {@see is_dir} {@see TemplateFiller2Test::testRunPharRelative2()} falla.
     * @param string $input_dir
     * @return false|string
     * @throws Exception
     */
    public static function isDirForPhar(string $input_dir) : bool {
        if ( $input_dir = realpath($input_dir) )
            if ( is_dir($input_dir) )
                return true;
        return false;

    }

}