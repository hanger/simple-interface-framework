# simple-interface-framework
a simple php server interface framework

this framework is just used for personal convenience,anyone want to fork is welcome,and wish for your request to make it better

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


#cache
the framework has reserved some cache scheme like memcache and memcached，the example yet not use,this will come true later
