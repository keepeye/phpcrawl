<?php
/**
 * main.php.
 * @author keepeye <carlton.cheng@foxmail>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
require_once __DIR__.'/autoload.php';

use library\Factory;

define('DATA_DIR',__DIR__.'/data');
define('COMMON_DIR',__DIR__.'/common');

$app = Factory::createApplication();
$app->run();