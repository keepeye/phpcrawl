<?php
/**
 * configs.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
return [
    'process_max' => 15,//最大进程数量
    //定义蜘蛛
    'spiders' => [
        //xigua 是蜘蛛名,唯一
        'xigua' => [
            'class' => 'spider\XiguaSpider',//类名,使用的命名空间,在spider目录下
            'charSet' => 'gbk',//目标网页编码,采集下来会自动转成utf-8
        ]
    ]
];