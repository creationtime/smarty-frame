# smarty-frame
运用php构建自己的框架

调用方式：全部记录在application/Web/Controllers/WelcomeController下

目录结构：：
根目录/application:应用程序核心目录

根目录/configs下：
    config:应用配置文件
    db:数据库配置文件
    routes:路由配置文件
    fileCache:文件缓存配置
    redisCache：redis缓存配置
    rabbitMQ：rabbitMQ消息代理配置

根目录/src下：考虑将来框架可能还要支持php脚本的执行，所以要将base和web分开，将来要加php脚本时，只需要建立一个console的文件夹就好了。
    common：存放公共调用文件
        Common：公共函数
        Env：公共调用请求类
    base：存放基础的类
        base下：
            Application：框架底层基础类
                        一个抽象类，实现了一个简单的run方法，run方法就是去执行以下handleRequest方法。
                        定义了一个抽象方法handleRequest，等待被继承，实现。
            Controller：包含控制器逻辑的基类
                       不需要每写一个要去渲染页面的action，都要去找相应路径的view，然后把它require进来。
                       所以抽象出一个Controller的基类，实现一个渲染页面的方法，让其他的controller继承，就可以使用相应的方法。
            Error：框架错误类
            Route：路由控制实现类
            Component:基类，用于存放必须引入的但是有些类没有的方法，防止调用出错
            Csrf：csrf防御，待实现

    cache:存放缓存相关类
        CacheInterface:缓存接口类，定义接口参数
        FileCache：文件缓存类
                   主要思想就是，每一个 key 都对应一个文件，缓存的内容序列化一下，存入到文件中，取出时再反序列化一下。剩下的基本都是相应的文件操作了。
        Redis:redis缓存类

    libs:第三方插件
        Mail:邮件类
        Upload:上传类
        PhpOffice：文件导入、导出类
            PhpExcel
            PhpSpreadsheet

    logs:日志文件，路径定义ROOT_PATH.'/'.config('LOG_PATH').'/'.date('Y').'/'.date('Ym').'/'.date('Ymd').'.log'

    web：存放与web相关的类
        Application：与web相关的类
                    所有程序都经过这里
        Controller：包含控制器逻辑的基类
                   不需要每写一个要去渲染页面的action，都要去找相应路径的view，然后把它require进来。
                   所以抽象出一个Controller的基类，实现一个渲染页面的方法，让其他的controller继承，就可以使用相应的方法。

    view:存放视图文件相关类
        Compiler：网上找的一个自己写的模板转换源码
        Smarty：引入smarty进行模板编译，并重新配置smarty
        （其实原理都一样，就是将模板中特定字符串正则解析成php代码，然后将转换后的模板内容保存起来，供下次调用）

    db：数据库交互Model类，支持防注入xss
        ModelInterface：数据模型接口类
        Model：数据模型基类，继承此类可由Model层直接进行数据交互
        Connection：连接类，用于数据库、缓存等创建连接实例
        DbClient:调用此类进行数据交互

    message:消息中间件
        RabbitMQ：简易封装类

    unit:部件
        rsaKey：rsa密钥不同位数文件
        Encrypt:对称加解密类
        Rsa：非对称加解密类
        Sms:短信发送类
        HttpCurl： Curl模拟Http工具类

    request:请求类
        Request：请求调用入口

    Sf:帮助类，提供公共框架功能。
    Helper：框架函数调用类

根目录/public：入口文件、静态文件夹

根目录/runtime：
    cache：缓存文件夹

根目录/vendor:composer文件夹



路由规则(省略了模块名，及 域名/api（没有则默认为web模块）/方法名?参数)：
    vbox-selfframe.cn/api/index
    vbox-selfframe.cn/index
    vbox-selfframe.cn/smarty-view
    vbox-selfframe.cn/backend/index


shell之创建文件夹：
[root@vbox-nginx shell_command]# vi mkdir.sh
#!/bin/sh

parentDir="/media/sf_Project/self/smarty-frame/application/$1"
fileName=$2
dirAndName=$parentDir/$fileName
if [ ! -d "$dirAndName" ];then
mkdir $dirAndName
echo "创建文件夹成功"
else
echo "文件夹已经存在"
fi

调用shell创建文件夹：
[root@vbox-nginx shell_command]# ./mkdir.sh ApiLoveHouse Model		//上级文件夹 要创建的文件夹名
创建文件夹成功


shell之创建php文件：
[root@vbox-nginx shell_command]# vi mkfile.sh
#!/bin/sh

parentDir="/media/sf_Project/self/smarty-frame/application/$1"
fileName=$2
dirAndName="$parentDir/$fileName.php"
string=${parentDir#*application}
namespace=$(echo $string | sed 's#\/#\\#g')
if [ ! -d "$parentDir" ];then
echo "父级文件夹路径错误"
else
cd $parentDir

if [ ! -f "$dirAndName" ];then

touch $dirAndName
echo "<?php" > $dirAndName

if [[ $fileName == *$strCon* ]];then
touch $dirAndName
echo "<?php" > $dirAndName

if [[ $fileName == *$strCon* ]];then
echo "namespace App$namespace;" >> $dirAndName
elif [[ $fileName == *$strMod* ]];then
echo "namespace App\$namespace;" >> $dirAndName
else
echo "当前只能创建controller和model文件"
fi

echo "" >> $dirAndName
echo "class $fileName{" >> $dirAndName
echo "          //" >> $dirAndName
echo "}" >> $dirAndName
echo "?>" >> $dirAndName
echo "文件创建完成"
else
echo "文件已经存在"
fi
fi
fi

调用shell创建文件：
[root@vbox-nginx shell_command]# ./mkfile.sh ApiLoveHouse/Controllers WelcomeController		//上级文件夹 要创建的文件名
文件创建完成
