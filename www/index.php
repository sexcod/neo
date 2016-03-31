<?php
/**
 * Limp - less is more in PHP
 * @copyright   Bill Rocha - http://google.com/+BillRocha
 * @license     MIT
 * @author      Bill Rocha - prbr@ymail.com
 * @version     0.0.1
 * @package     Limp
 * @access      public
 * @since       0.3.0
 *
 * The MIT License
 *
 * Copyright 2015 http://google.com/+BillRocha.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

// Defaults
error_reporting(E_ALL ^ E_STRICT);
setlocale (LC_ALL, 'pt_BR');
mb_internal_encoding('UTF-8');
date_default_timezone_set('America/Sao_Paulo'); 

// Developer only
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('track_errors', '1');

// Constants
// Path to WWW
define('_WWW', str_replace('\\', '/', strpos(__DIR__, 'phar://') !== false
                    ? dirname(str_replace('phar://', '', __DIR__)).'/'
                    : __DIR__.'/'));
define('_PHAR', (strpos(_WWW, 'phar://') !== false) ? _WWW : false); //Path if PHAR mode or false
define('_PHP', dirname(_WWW).'/php/');       //Path to Application
define('_HTML', dirname(_WWW).'/html/');     //Path to HTML files

// Composer autoload
include __DIR__.'/vendor/autoload.php';

// ------- optional - replace with your favorite libraries/solutions 

// Error/Exception
set_error_handler(['Neos\Debug','errorHandler']);
set_exception_handler(['Neos\Debug', 'exceptionHandler']);

// Cli mode
if(php_sapi_name() === 'cli') return new Neos\Cli($argv);

//Router config
include _PHP.'Config/router.php';

//Runnig ...
Neos\Router::this()->run();
