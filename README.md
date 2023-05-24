# think-log-viewer
thinkphp6 log日志的视图扩展包


### 1.配置路由
~~~
Route::get('log_view', "\Jhansin\ThinkLogViewer\LogServer@index");
~~~



### 2.运行thinkphp服务
~~~ 
php think run
~~~



### 3.访问浏览器 `http://127.0.0.1:8000/log_view`即可