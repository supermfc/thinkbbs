<?php
declare (strict_types = 1);

namespace app\index\controller;

use think\Request;
use app\common\model\User;
use app\common\model\Sms;

class Register extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        return $this->fetch('create');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(User $user)
    {
        //如果不是POST请求或者不是Ajax请求
        if (!$this->request->isPost() || !$this->request->isAjax()) {
                return $this->error('对不起，你访问页面不存在');
        }

        try {
            // 保存表单提交数据
            $param = $this->request->post();
            $user = User::register($param);
            //$user->save($this->request->post());
        } catch (\Exception $e) {
            return $this->error('对不起，注册失败。');
        }

        // 注册成功后跳转到首页
        return $this->success('恭喜你注册成功。', '/');
        /*
        $user->save($this->request->post());
        return $this->redirect('/');
        */
    }

    /**
     * 验证字段值是否唯一
     * @Author   zhanghong(Laifuzi)
     */
    public function check_unique()
    {
        if (!$this->request->isPost() || !$this->request->isAjax()) {
            $this->redirect('[page.signup]');
        }

        $param = $this->request->post();
        $is_valid = User::checkFieldUnique($param);
        if ($is_valid) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    /**
     * 发送注册验证码
     * @Author   zhanghong(Laifuzi)
     */
    public function send_code()
    {
        if (!$this->request->isAjax()) {
            return $this->redirect('[page.signup]');
        } else if (!$this->request->isPost()) {
            return $this->error('对不起，你访问页面不存在。');
        }

        $mobile = $this->request->post('mobile');
        if (empty($mobile)) {
            return $this->error('对不起，注册手机号码不能为空。');
        }
        $param = ['name' => 'mobile', 'mobile' => $mobile];
        if (User::checkFieldUnique($param)) {
            return $this->error('对不起，你填写的手机号码已注册。');
        }

        try {
            $sms = new Sms();
            $sms->sendCode($mobile);
        } catch (\Exception $e) {
            // 捕获所有异常类，包括 NoGatewayAvailableException
            return $this->error($e->getMessage());
        }

        return $this->success('验证码发送成功。');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
