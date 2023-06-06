# think-log-viewer
thinkphp6 log日志的视图扩展包

## 组件安装

### 1.安装组件
~~~
composer require jhansin/think-log-viewer
~~~

### 2.添加环境变量
~~~
[LOG]
LOGPATH = "/www/***/log"
~~~

### 3.配置路由
~~~
Route::get('log_view', "\Jhansin\ThinkLogViewer\LogServer@index");
~~~



## 运行thinkphp服务

### 1.运行项目
~~~ 
php think run
~~~

### 2.访问浏览器 `http://127.0.0.1:8000/log_view`即可



## 样式

![20230606095624.png](https://s2.loli.net/2023/06/06/cTHp6WKU1Gh5843.png)