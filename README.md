说明
------

仍在开发中...不过可以简单玩玩了


安装
------

    git clone https://github.com/keepeye/phpcrawl.git ./phpcrawl
    cd phpcrawl
    composer install
    
使用
------

###第一步:定义蜘蛛###

打开 configs.php ,在 spider 下定义蜘蛛, 说明参照注释

###第二步:创建spider类###

在配置文件中定义了一个 spider 后,就需要创建对应的 class ,class继承 `library\Spider` ,需要实现的方法请参考示例蜘蛛 `XiguaSpider` 

###第三步:启动爬虫###

在项目根目录执行 

    php main.php crawl 蜘蛛名
    //例: php main.php crawl xigua
    
###第四步:查看数据###

在 data 目录下又对应蜘蛛名的子目录,数据会保存到sqlite, 可以用工具查看.

###未完###

重新采集只需要删除 `data/蜘蛛名` 目录

等待支持的特性有:

- 图片下载
- 内容页分页采集
- 多级内容页面采集
- 发布模块
- 更多..