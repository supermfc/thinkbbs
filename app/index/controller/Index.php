<?php
declare (strict_types = 1);

namespace app\index\controller;

use tpadmin\model\Config as ConfigModel;
use app\common\model\Sms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class Index extends Base
{
    public function index()
    {
        //return '您好！这是一个[index]示例应用';
        /*
        将site赋值放置到基类里面
        $config = ConfigModel::where('name', ConfigModel::NAME_SITE_SETTING)->find();
        if (empty($config) || empty($config->value)) {
            $site = [];
        } else {
            $site = json_decode($config->value, true);
        }

        return view('index', [
            'site'  => $site,
        ]);*/
        return view('index');
    }

    //发送短信的测试方法
    public function sms(){
    	$mobile = '15639101113';
    	$status = true;
    	$message = '短信发送成功';
        try {
            $sms = new Sms();
            $sms->sendCode($mobile);
        } catch (NoGatewayAvailableException $e) {
            $status = false;
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
        }
        return json(['status' => $status, 'message' => $message]);
    }
}
