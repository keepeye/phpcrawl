<?php
/**
 * Command.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace library;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


abstract class Command extends BaseCommand {
    //命令初始化
    public function initialize(InputInterface $input, OutputInterface $output)
    {

    }
}
