<?php
/**
 * Request.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace library;

use library\exception\RequestException;
use library\Response;

//TODO
class Request
{
    public $url;
    public $callback;
    public $encoding;

    protected $defaultCallback = 'parse';

    public function __construct($url, $encoding=null, $callback = null)
    {
        $this->url = $url;
        $this->callback = $callback;
        $this->encoding = $encoding;
    }

    /**
     * 发起请求并返回响应对象
     *
     * @return \library\Response
     * @throws RequestException
     */
    public function request()
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new RequestException(curl_error($ch));
        }
        if (($httpCode=curl_getinfo($ch, CURLINFO_HTTP_CODE)) != '200') {
            throw new RequestException("http返回码:{$httpCode}");
        }
        curl_close($ch);
        list($headers,$body) = explode("\r\n\r\n",$result,2);
        //编码转换
        if ($this->encoding && $this->encoding != 'utf-8') {
            $body = iconv($this->encoding,"utf-8",$body);
        }
        $response = new Response($this->url);
        $response->setRawHeaders($headers);
        $response->setRawBody($body);
        if (isset($this->callback)) {
            $this->callback($response);
        }
        return $response;
    }
}

