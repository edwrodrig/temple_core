labo86\temple_core
========
Una biblioteca para procesar plantillas de proyectos.

[![Latest Stable Version](https://poser.pugx.org/labo86/temple_core/v/stable)](https://packagist.org/packages/labo86/temple_core)
[![Total Downloads](https://poser.pugx.org/labo86/temple_core/downloads)](https://packagist.org/packages/labo86/temple_core)
[![License](https://poser.pugx.org/labo86/temple_core/license)](https://github.com/labo86/temple_core/blob/master/LICENSE)
[![Build Status](https://travis-ci.org/labo86/temple_core.svg?branch=master)](https://travis-ci.org/labo86/temple_core)
[![codecov.io Code Coverage](https://codecov.io/gh/labo86/temple_core/branch/master/graph/badge.svg)](https://codecov.io/github/labo86/temple_core?branch=master)
[![Code Climate](https://codeclimate.com/github/labo86/temple_core/badges/gpa.svg)](https://codeclimate.com/github/labo86/temple_core)
![Hecho en Chile](https://img.shields.io/badge/country-Chile-red)

## Uso
Suponiendo que tenemos un directorio `input_dir` con archivos de cualquier tipo.
```php
use \labo86\temple_core\TemplateFiller;

$company = 'labo86';
$project = 'project';
$filler = new TemplateFiller($company, $project);

//informamos que los archivos o carpetas con nombre .git o ignored_file serán ignorados
$filler->ignore('.git', 'ignored_file');

//construimos el proyecto en base a la plantilla
$filler->fillTemplate('input_dir', 'output_dir');
```
El código anterior generará una nueva carpeta en `output_dir` en donde reemplazará tanto nombres como contenidos según reglas de reemplazo de plantillas.

Las reglas de reemplazo son las siguientes:
 - Las ocurrencias de `tpl_company_tpl` serán reemplazadas por el <strong>nombre de la compañía</strong> especificada.
 - Las ocurrencias de `tpl_project_tpl` serán reemplazadas por el <strong>nombre de proyecto</strong> especificada.

## Instalación como biblioteca
```shell script
composer require labo86/temple_core
```

## Instalación como ejecutable
Se puede construir un ejecutable con [make_phar.php](https://github.com/labo86/hapi_core/blob/master/scripts/make_phar.php) usando el siguiente comando:
```shell script
php -d phar.readonly=Off scripts/make_phar.php
```
Hay una [run configuration](https://www.jetbrains.com/help/phpstorm/run-debug-configuration.html) de PhpStorm que lanza el comando anterior.

El comando anterior construirá <code>temple_core.phar</code> que se podrá lanzar de la siguiente manera:
```shell script
php temple_core.phar company project input_dir output_dir
```
Los argumentos corresponden a cada una de los variables recibidas por la clase [TemplateFiller](https://github.com/labo86/hapi_core/blob/master/src/TemplateFiller.php)


## Información de mi máquina de desarrollo
Salida de [system_info.sh](https://github.com/labo86/hapi_core/blob/master/scripts/system_info.sh)
```
+ hostnamectl
+ grep -e 'Operating System:' -e Kernel:
  Operating System: Ubuntu 20.04 LTS
            Kernel: Linux 5.4.0-33-generic
+ php --version
PHP 7.4.3 (cli) (built: May 26 2020 12:24:22) ( NTS )
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
  

