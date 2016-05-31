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
    abstract public function startRequests();//初始列表页url
    abstract public function getFields();//定义item字段
    abstract public function parseItemUrls(Response $response);//提取内容页url
    abstract public function parseItem(Response $response);//解析内容页标签

    public $name;//蜘蛛名字,跟配置文件和命令行指定的一致,不要修改
    protected $configs;

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

    //初始化数据库
    public function initDatabase()
    {
        $dataDir = DATA_DIR.'/'.$this->name;
        $dns = sprintf("sqlite:%s/items.sqlite",$dataDir);
        //数据库已存在,不作处理
        if (!file_exists($dataDir)) {
            @mkdir($dataDir,0755,true);
            DB::connect($dns);
            $sql = <<<EOF
            CREATE TABLE `items` (
                `id`	INTEGER PRIMARY KEY AUTOINCREMENT,
                `url`	TEXT NOT NULL DEFAULT '' UNIQUE,
                `status`	INTEGER DEFAULT '0',
                ##fields##,
                `created_at`	TEXT DEFAULT ''
            );
EOF;
            $fields = $this->getFields();
            $fields = array_map(function($name){
                return "`{$name}` TEXT DEFAULT ''";
            },$fields);
            $sql = str_replace("##fields##",implode(",\n",$fields),$sql);
            //创建表
            DB::$instance->exec($sql);
            //创建索引
            DB::$instance->exec("CREATE INDEX `items_status` ON `items`(`status`)");
        } else {
            DB::connect($dns);
        }
    }

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
     * 设置爬虫对象
     *
     * @param \library\Crawl $crawl
     */
    public function setCrawl(Crawl $crawl)
    {
        $this->crawl = $crawl;
    }

    //读取配置参数
    public function getConfig($key,$default='')
    {
        return isset($this->configs[$key]) ? $this->configs[$key] : $default;
    }

    //分析内容页url并入库
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

    //获取待采集的item
    public function getBlankItems($num)
    {
        return DB::$instance->query("select * from items where status = 0 limit ?",[$num]);
    }

    //分析内容页内容
    public function saveItem($id,Response $response)
    {
        $data = $this->parseItem($response);
        $data['status'] = '1';
        DB::$instance->update('items',$id,$data);
    }
}
