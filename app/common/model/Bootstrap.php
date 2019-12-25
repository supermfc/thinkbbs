<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\paginator\driver\Bootstrap as Base;

/**
 * @mixin think\Model
 */
class Bootstrap extends Base
{
    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getAvailablePageWrapper(string $url, string $page): string
    {
        return '<li class="page-item"><a class="page-link" href="' . htmlentities($url) . '">' . $page . '</a></li>';
    }

    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper(string $text): string
    {
        return '<li class="page-item disabled"><a class="page-link" href="javascript:void(0);">' . $text . '</a></li>';
    }

    /**
     * 生成一个激活的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper(string $text): string
    {
        return '<li class="page-item active"><a class="page-link" href="javascript:void(0);">' . $text . '</a></li>';
    }
}
