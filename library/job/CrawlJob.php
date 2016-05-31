<?php
/**
 * CrawlJob.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace library\job;

use library\Job;
use library\Request;

class CrawlJob extends Job
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    //处理任务
    public function handle()
    {
        $response = $this->request->request();
        $params = [$response];
        $callback = $this->request->getCallback();

        //匿名函数啥的直接调用
        if (is_callable($callback)) {
            return call_user_func_array($callback,$params);
        } else {
            //字符串的话则当做spider类里的方法名
            return call_user_func_array([$this->spider,$callback],$params);
        }
    }
}
