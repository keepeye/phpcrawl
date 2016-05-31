<?php
/**
 * configs.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
return [
    'process_max' => 15,
    'spiders' => [
        'xigua' => [
            'class' => 'spider\XiguaSpider',
            'charSet' => 'gbk'
        ]
    ]
];