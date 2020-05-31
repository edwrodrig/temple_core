edwrodrig\temple_core
========
Una biblioteca para procesar plantillas de proyectos.

[![Latest Stable Version](https://poser.pugx.org/edwrodrig/temple_core/v/stable)](https://packagist.org/packages/edwrodrig/temple_core)
[![Total Downloads](https://poser.pugx.org/edwrodrig/temple_core/downloads)](https://packagist.org/packages/edwrodrig/temple_core)
[![License](https://poser.pugx.org/edwrodrig/temple_core/license)](https://github.com/edwrodrig/temple_core/blob/master/LICENSE)
[![Build Status](https://travis-ci.org/edwrodrig/temple_core.svg?branch=master)](https://travis-ci.org/edwrodrig/temple_core)
[![codecov.io Code Coverage](https://codecov.io/gh/edwrodrig/temple_core/branch/master/graph/badge.svg)](https://codecov.io/github/edwrodrig/temple_core?branch=master)
[![Code Climate](https://codeclimate.com/github/edwrodrig/temple_core/badges/gpa.svg)](https://codeclimate.com/github/edwrodrig/temple_core)
![Hecho en Chile](https://img.shields.io/badge/country-Chile-red)


## Uso
Suponiendo que tenemos un directorio `input_dir` con archivos de cualquier tipo.
```php
use \edwrodrig\temple_core\TemplateFiller;

$company = 'edwrodrig';
$project = 'project';
$filler = new TemplateFiller('edwrodrig', 'project');

//informamos que los archivos o carpetas con nombre .git o ignored_file serán ignorados
$filler->ignore('.git', 'ignored_file');

//construimos template
if ( $filler->fillTemplate('input_dir', 'output_dir') ) {
    echo "success\n";
}
```
El código anterior generará una nueva carpeta en `output_dir` en donde reemplazará tanto nombres como contenidos según reglas de reemplazo de platillas.

Las reglas de reemplazo son las siguientes:
 - Las ocurrencias de `tpl_company_tpl` serán reemplazadas por el <strong>nombre de la compañía</strong> especificada.
 - Las ocurrencias de `tpl_project_tpl` serán reemplazadas por el <strong>nombre de proyecto</strong> especificada.

## Instalación
```shell script
composer require edwrodrig/temple_core
```

## Información de mi máquina de desarrollo
Salida de [system_info.sh](https://github.com/edwrodrig/hapi_core/blob/master/scripts/system_info.sh)
```
  Operating System: Ubuntu 20.04 LTS
            Kernel: Linux 5.4.0-31-generic
PHP 7.4.3 (cli) (built: May  5 2020 12:14:27) ( NTS )
Copyright (c) The PHP Group
Zend Engine v3.4.0, Copyright (c) Zend Technologies
    with Zend OPcache v7.4.3, Copyright (c), by Zend Technologies
    with Xdebug v2.9.2, Copyright (c) 2002-2020, by Derick Rethans
```

## Notas
  - El código se apega a las recomendaciones de estilo de [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md).
  - Este proyecto esta pensado para ser trabajado usando [PhpStorm](https://www.jetbrains.com/phpstorm).
  - Se usa [PHPUnit](https://phpunit.de/) para las pruebas unitarias de código.
  - Para la documentación se utiliza el estilo de [phpDocumentor](http://docs.phpdoc.org/references/phpdoc/basic-syntax.html). 
  

