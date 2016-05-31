<?php
/**
 * Job.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace library;

use library\Spider;

abstract class Job
{
    protected $spider;

    abstract public function handle();

    public function setSpider(Spider $spider)
    {
        $this->spider = $spider;
    }
}