<?php

namespace app\controller;

use shisou\tpinit\controller\_CmsController;
use think\Exception;
use app\traits\commonWeb;

/**
 * 栏目控制器
 * @version 20220522
 */
class CateController extends _Controller
{
    use commonWeb;

    /**
     * 栏目首页
     * @return \think\response\View
     * @throws Exception
     */
    public function indexAction()
    {
        $this->globals['row'] = $this->curCate;

        return $this->view();
    }
}
