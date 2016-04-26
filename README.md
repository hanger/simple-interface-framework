# simple-interface-framework
a simple php server interface framework

该框架可以初始设计是用于中小项目服务端接口，返回数据格式为json，没有压缩，如需压缩数据，请自行修改'framework/action/ABaseAction.php' 中execute方法

this framework is just used for personal convenience,anyone want to fork is welcome,and wish for your request to make it better

该框架只是个人项目抽离出来的，如果有兴趣欢迎使用，并根据自己的项目自行调整，欢迎提交request帮助完善

#use guide

your-domain/index.php?a=xxx&cmd=xxx

#example
your-domain/?a=test&cmd=1

#test
the TestAction is a simple example just for familiar to this framework in 'app/action/TestAction.php'

#db-config
the database config file is 'framework/db/db_config.ini'
this framework support mysqli and pdo_mysql,you can choose which one to default by change the constant in 'framework/const/SettingConst.php'
all these are realized by prepared statement with place holder for safe purpose

该框架包含对mysql的封装以及对mongo的简单封装（测试代码中未使用mongo），复杂sql可以直接书写，其余全部用prepare statement封装来确保mysql访问安全，防止注入攻击

#cache
the framework has reserved some cache scheme like memcache and memcached，the example yet not use,this will come true later

该框架提供了对memcache以及memcached的封装，测试用例中未连接使用，可根据自己的项目自行添加调用