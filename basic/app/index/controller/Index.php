<?php
namespace app\index\controller;

use think\Cache;
use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {

        $url = HOST."/h5/h5.html#";
        return $this->redirect($url);
    }
}
