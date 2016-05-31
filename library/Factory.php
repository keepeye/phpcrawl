<?php
/**
 * Factory.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace library;

use library\Application;
use library\commands\CrawlCommand;

class Factory
{
    public static function createApplication()
    {
        $app = new Application();
        $app->add(new CrawlCommand());
        return $app;
    }
}