<?php
/**
 * Response.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace library;
use library\Utils;

//TODO
class Response
{
    public $rawBody;
    public $rawHeaders;
    public $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function setRawBody($data)
    {
        $this->rawBody = $data;
    }
    
    public function setRawHeaders($headers)
    {
        $this->rawHeaders = $headers;
    }

    //完整url拼接
    public function urlJoin($rel,$base='')
    {
        if (!$base) {
            $base = $this->url;
        }
        return Utils::url2abs($rel,$base);
    }
}
