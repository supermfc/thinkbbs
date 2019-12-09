<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use tpadmin\model\Config as TpadminConfig;
/**
 * @mixin think\Model
 */
class Config extends TpadminConfig
{
    /**
     * 读取站点设置信息
     * @Author   zhanghong(Laifuzi)
     * @return   array             站点设置信息
     */
    public static function siteSetting(): array
    {
        $config = self::where('name', self::NAME_SITE_SETTING)->find();
        if (empty($config)) {
            return [];
        }

        //TpadminConfig的 getSettingsAttr 方法获取settings 设置
        return $config->settings;
    }
}
