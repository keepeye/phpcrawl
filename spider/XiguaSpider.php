<?php
/**
 * XiguaSpider.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace spider;

use library\Response;
use library\Spider;
use library\Request;

include_once COMMON_DIR.'/phpQuery/phpQuery.php';

class XiguaSpider extends Spider
{
    public $startUrls = ['http://www.ttll.cc/dy/index.html','http://www.ttll.cc/dy/index2.html','http://www.ttll.cc/dy/index3.html'];

    /**
     * 生成列表页url
     *
     * @return \Generator
     */
    public function startRequests()
    {
        foreach ($this->startUrls as $url) {
            yield new Request($url,$this->getConfig('charSet','utf-8'));
        }
    }

    /**
     * 解析内容页url
     * 
     * @param Response $response 列表页响应对象
     * @return \Generator
     * @throws \Exception
     */
    public function parseItemUrls(Response $response)
    {
        \phpQuery::newDocument($response->rawBody);
        foreach (\phpQuery::pq("div#tabcontentsort1 > ul > li > a") as $anchor) {
            yield $response->urlJoin(\pq($anchor)->attr('href'));
        }
    }

    /**
     * 解析内容页
     *
     * @param \library\Response $response 内容页响应
     * @return mixed
     */
    public function parseItem(Response $response)
    {
        \phpQuery::newDocument($response->rawBody);
        return [
            'title' =>\pq("title")->html()
        ];
    }
}
