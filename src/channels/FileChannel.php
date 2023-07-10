<?php

namespace Jhansin\ThinkLogViewer\channels;

class FileChannel
{
    private $config = [];

    protected $log_path;

    protected $all_log = [];

    protected $param = [];

    protected $log_file = '';

    protected $content = '';

    protected $content_arr = [];

    protected $splice_content_arr = [];

    protected $total = 0;

    protected $totalPage = 0;

    public function __construct($channel)
    {
        $this->config = $channel;
        $this->initLog();
        $this->loadParam();
        $this->loadLog();
    }

    public function view()
    {
        include_once __DIR__ . "/../view/index.php";
    }

    private function initLog()
    {
        $this->log_path = $this->config['path'];
        $this->all_log = $this->getDirs($this->log_path);
    }

    private function loadParam()
    {
        $this->param = array_merge(request()->get(), ['page' => $this->getPage()]);
    }

    private function loadLog()
    {
        $this->log_file = $this->log_path . "/" . ($this->param['file'] ?? '');
        $this->content = (file_exists($this->log_file) && is_file($this->log_file)) ? file_get_contents($this->log_file) : "";
        foreach (array_filter(explode(PHP_EOL, $this->content)) as $k => $v) {
            preg_match("/\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}/", $v, $times);
            $this->content_arr[$k]['time'] = $times[0] ?? '';
            if (preg_match("/(.*?)\[debug\](.*?)$/i", $v, $match)) {
                $this->content_arr[$k]['level'] = 'debug';
            } elseif (preg_match("/(.*?)\[info\](.*?)$/i", $v, $match)) {
                $this->content_arr[$k]['level'] = 'info';
            } elseif (preg_match("/(.*?)\[warning\](.*?)$/i", $v, $match)) {
                $this->content_arr[$k]['level'] = 'warning';
            } elseif (preg_match("/(.*?)\[error\](.*?)$/", $v, $match)) {
                $this->content_arr[$k]['level'] = 'error';
            } elseif (preg_match("/(.*?)\[sql\](.*?)$/i", $v, $match)) {
                $this->content_arr[$k]['level'] = 'sql';
            } else {
                $this->content_arr[$k]['level'] = 'all';
            }
            $this->content_arr[$k]['content'] = $match[2] ?? $v;
        }
        $last_names = array_column($this->content_arr, 'time');
        array_multisort($last_names, SORT_DESC, $this->content_arr);
        //数组反转
        // $this->content_arr = array_reverse($this->content_arr,true);
        //数组总数
        $this->total = count($this->content_arr);

        //总页数
        $this->totalPage = ceil($this->total / $this->getLimit());
        //切片后的数组
        $this->splice_content_arr = array_slice($this->content_arr, ($this->getPage() - 1) * $this->getLimit(), $this->getLimit());
    }

    private function getPage()
    {
        return request()->get('page', 1);
    }

    private function getLimit()
    {
        return request()->get('limit', 15);
    }

    private function getDirs(string $dir): array
    {
        $files = array();
        if ($handle = opendir($dir)) {
            while (($file = readdir($handle)) !== false) {
                if ($file != ".." && $file != ".") {
                    if (is_dir($dir . "/" . $file)) {
                        $files[$file] = $this->getDirs($dir . "/" . $file);
                    } else {
                        //日志文件超出大小后，建立新的日志文件
                        if (strpos($file, "-")) {
                            $name = explode("-", $file)[1];
                            $date = explode("_", $name);
                            $month = substr($date[0],0,6);
                            $day = substr($date[0],6);
                        } else {
                            $date = explode("_", $file);
                            $month = substr($date[0], 0, 6);
                            $day = substr($date[0], 6);
                        }
                        $files[$month][$day][] = $file;
                    }
                }
            }
            closedir($handle);
        }
        krsort($files);
        return $files;
    }
}