<?php
/**
 * autoload.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

require_once __DIR__.'/vendor/autoload.php';

$classLoader = new Symfony\Component\ClassLoader\Psr4ClassLoader();
$classLoader->addPrefix('library\\',__DIR__.'/library');
$classLoader->addPrefix('spider\\',__DIR__.'/spider');
$classLoader->addPrefix('common\\',__DIR__.'/common');
$classLoader->register();