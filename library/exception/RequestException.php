<?php
/**
 * RequestException.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace library\exception;


class RequestException extends \Exception
{
    public function __construct($message, $code=0, Exception $previous=null)
    {
        $message = "请求异常:".$message;
        parent::__construct($message, $code, $previous);
    }
}