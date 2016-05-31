<?php
/**
 * Crawl.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace library;

use library\job\CrawlJob;
use Symfony\Component\Console\Output\OutputInterface;
use library\Utils;
use library\Spider;
use library\Item;

class Crawl
{
    /**
     * 蜘蛛
     *
     * @var Spider
     */
    protected $spider;
    /**
     * 控制台输出
     *
     * @var OutputInterface
     */
    protected $output;
    /**
     * 配置参数
     *
     * @var array
     */
    public $configs;

    /**
     * 采集内容页计数器
     *
     * @var int
     */
    private $itemCounts=0;

    /**
     * 启动时间
     *
     * @var int
     */
    private $startTime;

    private $pids=array();
    private $defaultProcessMax=1;

    /**
     * Crawl constructor.
     *
     * @param string $spider 蜘蛛名字
     * @param array $configs 配置参数
     */
    public function __construct($spider, $configs = array())
    {
        $this->configs = $configs;
        $this->spider = $this->createSpider($spider);
    }

    /**
     * 设置控制台输出器
     *
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * 创建蜘蛛
     *
     * @param string $name 蜘蛛名称
     * @return mixed
     * @throws \Exception
     */
    protected function createSpider($name)
    {
        if (!isset($this->configs['spiders'][$name])) {
            throw new \Exception("未找到蜘蛛,请在配置文件中定义");
        }
        $config = $this->configs['spiders'][$name];
        if (!isset($config['class'])) {
            throw new \Exception("蜘蛛配置错误,缺少class参数");
        }
        $spider = new $config['class']($name,$config);
        $spider->setCrawl($this);
        return $spider;
    }


    /**
     * 启动爬虫
     */
    public function run()
    {
        $this->startTime = microtime(true);
        $startRequests = $this->spider->startRequests();
        //STEP1 采集内容页url
        foreach ($startRequests as $request) {
            $this->spider->crawlItemUrls($request->request($request));
        }
        $this->output->writeln("列表页采集完毕...开始采集内容页...");
        //STEP2 多进程采集内容页
        $this->crawlItems();
        //采集完毕
        $this->output->writeln("采集完毕,共采集了 {$this->itemCounts} 个内容页,耗时 ".round(microtime(true) - $this->startTime,2)." 秒");
        exit(0);
    }

    protected function crawlItems()
    {
        //最大子进程数量
        $processMax = $this->getProcessMax();
        if ($processMax <= 0) {
            throw new \Exception('子进程最大数量为:'.$processMax);
        }
        //获取最大子进程数量的待采集item
        $blankItems = $this->spider->getBlankItems($processMax);
        if (empty($blankItems)) {
            return;
        }
        for ($i=0;$i<count($blankItems);$i++) {
            //子进程处理队列
            $pid = pcntl_fork();
            if ($pid == -1) {
                die('子进程创建失败');
            } elseif ($pid > 0) {
                //主进程
                $this->pids[$pid] = $pid;
            } else {
                //子进程抓取
                $request = new Request($blankItems[$i]['url'],$this->spider->getConfig('charSet','utf-8'));
                $response = $request->request();
                $this->spider->saveItem($blankItems[$i]['id'],$response);
                $this->output->writeln("已处理:".$response->url);
                exit(0);
            }
        }

        foreach ($this->pids as $pid) {
            pcntl_waitpid($pid,$status);
        }
        $this->itemCounts += count($blankItems);
        $this->crawlItems();//递归处理下一批
    }

    /**
     * 获取最大进程数量
     *
     * @return int
     */
    public function getProcessMax()
    {
        return isset($this->configs['process_max']) ? (int) $this->configs['process_max'] : $this->defaultProcessMax;
    }
}
