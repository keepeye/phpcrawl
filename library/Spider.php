<?php
/**
 * Spider.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace library;

use library\Crawl;
use library\db\Blueprint;
use library\Request;
use library\Response;
use library\db\DB;

abstract class Spider
{
    /**
     * 蜘蛛名字
     *
     * @var string
     */
    public $name;
    /**
     * 爬虫对象
     * @var Crawl
     */
    protected $crawl;
    /**
     * 初始url
     *
     * @var array
     */
    protected $startUrls = [];
    /**
     * 蜘蛛的配置
     *
     * @var array
     */
    protected $configs;

    /**
     * 生成列表页url
     *
     * @return \Generator
     */
    abstract public function startRequests();
    /**
     * 解析内容页url
     *
     * @param Response $response 列表页响应对象
     * @return \Generator
     * @throws \Exception
     */
    abstract public function parseItemUrls(Response $response);

    /**
     * 解析内容页
     *
     * @param \library\Response $response 内容页响应
     * @return mixed
     */
    abstract public function parseItem(Response $response);//解析内容页标签

    /**
     * Spider constructor.
     *
     * @param $name 蜘蛛名字
     * @param array $configs 配置参数
     */
    public function __construct($name,$configs=[])
    {
        $this->name = $name;
        $this->configs = $configs;
        $this->initDatabase();
        #额外的初始化工作
        $this->initialize();
    }

    //子类可以覆盖该方法做一些额外的初始化工作
    public function initialize(){}

    /**
     * 初始化数据库
     */
    public function initDatabase()
    {
        $dataDir = DATA_DIR.'/'.$this->name;
        $dataFile = $dataDir.'/items.sqlite';
        $dns = sprintf("sqlite:%s",$dataFile);
        //数据库不存在则创建
        if (!file_exists($dataFile)) {
            if (!file_exists($dataDir)) {
                @mkdir($dataDir,0755,true);
            }
            DB::connect($dns);
            $sql = <<<EOF
            CREATE TABLE `items` (
                `id`	INTEGER PRIMARY KEY AUTOINCREMENT,
                `url`	TEXT NOT NULL DEFAULT '' UNIQUE,
                `status`	INTEGER DEFAULT '0',
                `data` TEXT DEFAULT '',
                `created_at`	TEXT DEFAULT ''
            );
EOF;
            //创建表
            DB::$instance->exec($sql);
            //创建索引
            DB::$instance->exec("CREATE INDEX `items_status` ON `items`(`status`)");
        } else {
            DB::connect($dns);
        }
    }

    /**
     * 设置爬虫对象
     *
     * @param \library\Crawl $crawl
     */
    public function setCrawl(Crawl $crawl)
    {
        $this->crawl = $crawl;
    }

    /**
     * 读取配置
     *
     * @param $key
     * @param string $default
     * @return mixed|string
     */
    public function getConfig($key,$default='')
    {
        return isset($this->configs[$key]) ? $this->configs[$key] : $default;
    }

    /**
     * 提取内容页url
     *
     * @param \library\Response $response\
     */
    public function crawlItemUrls(Response $response)
    {
        $urls = $this->parseItemUrls($response);
        foreach ($urls as $url) {
            DB::$instance->insert('items',[
                'url' => $url,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * 从数据库中读取待采集的url
     *
     * @param $num
     * @return array
     */
    public function getBlankItems($num)
    {
        return DB::$instance->query("select * from items where status = 0 limit ?",[$num]);
    }

    /**
     * 采集内容页数据
     *
     * @param $id
     * @param \library\Response $response
     */
    public function saveItem($id,Response $response)
    {
        $items = $this->parseItem($response);
        $data['status'] = '1';
        $data['data'] = json_encode($items,JSON_UNESCAPED_UNICODE);
        DB::$instance->update('items',$id,$data);
    }
}
