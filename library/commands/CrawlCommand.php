<?php
/**
 * Crawl.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace library\commands;

use library\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use library\Crawl;

class CrawlCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('crawl')
            ->setDescription('启动爬虫')
            ->addArgument(
                'spider',
                InputArgument::REQUIRED,
                '蜘蛛名字'
            )
            ->addOption(
                'config',
                'c',
                InputOption::VALUE_OPTIONAL,
                '配置文件位置,相对入口文件路径',
                './configs.php'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argSpider = $input->getArgument('spider');
        $argConfig = $input->getOption('config');
        try {
            $configs = $this->loadConfig($argConfig);
            $crawl = new Crawl($argSpider,$configs);
            $crawl->setOutput($output);
            $crawl->run();
        } catch (\Exception $e) {
            echo $e;
        }
    }

    protected function loadConfig($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("配置文件不存在");
        }
        $configs = include_once $path;
        return $configs;
    }
}