<?php
declare (strict_types = 1);

namespace app\index\controller;

use think\Request;
use think\facade\Session;
use app\common\model\User;
use app\common\exception\ValidateException;

class Login extends Base
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
        $current_user = User::currentUser();
        if (!empty($current_user)) {
            return $this->redirect('/');
        }

        return $this->fetch('login/create');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        if (!$this->request->isPost() || !$this->request->isAjax()) {
            $message = '对不起，你访问页面不存在。';
            // 在跳转前把错误提示消息写入 session 里
            Session::flash('danger', $message);
            return $this->error($message, '[page.login]');
        }

        $mobile = $this->request->post('mobile');
        $password = $this->request->post('password');
        try {
            User::login($mobile, $password);
        } catch (ValidateException $e) {
            // $mobile 或 $password 错误，把错误信息返回给表单页面
            return $this->error('对不起，你填写的手机号码或密码不正确。', null, ['errors' => $e->getData()]);
        } catch (\Exception $e) {
            // 其它异常错误
            return $this->error($e->getMessage());
        }

        $currentUser = User::currentUser();
        $message = '欢迎你回来，'.$currentUser->name.'.';
        Session::set('success', $message);
        return $this->success($message, '/thinkbbs/public');
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
        if ($this->request->isPost()) {
            User::logout();
        }

        return $this->redirect('/thinkbbs/public');
    }
}
