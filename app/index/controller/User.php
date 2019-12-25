<?php
declare (strict_types = 1);

namespace app\index\controller;

use think\Request;
use think\facade\Session;
use app\common\model\User as UserModel;
use app\common\model\Topic as TopicModel;

class User extends Base
{
    //除了read方法，其它的都应用中间件
    protected $middleware = [
        'auth' => ['except' => ['read']],
    ];

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
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $user = UserModel::find(intval($id));
        if(empty($user)){
            // 当查看的用户不存在时跳转到首页
            $message = '对不起，你访问页面不存在。';
            Session::flash('danger', $message);
            return $this->redirect('/');
        }
        //dump($user);
        return $this->fetch('read', [
            'user' => $user,
            'topic_paginate' => TopicModel::minePaginate(['user_id' =>$user->id],5),
        ]);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit()
    {
        //使用中间件以后，下面语句可以删除
        //
        
        $currentUser = UserModel::currentUser();
        /*
        if (empty($currentUser)) {
            Session::flash('info', '请先登录系统。');
            return $this->redirect('[page.login]');
        } */

        return $this->fetch('edit', [
          'user' => $currentUser->refresh(),
        ]);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update()
    {
        //使用中间件后，以下语句可以删除
        /*
        $currentUser = UserModel::currentUser();
        if (empty($currentUser)) {
            Session::flash('info', '请先登录系统。');
        } else  */

        if (!$this->request->isAjax() || !$this->request->isPut() ) {
            Session::flash('danger', '对不起，你访问页面不存在。');
            return $this->redirect(url('[user.read]', ['id' => $currentUser->id]));
        }

        $data = $this->request->post();
        try {
            $currentUser->updateProfile($data);
        } catch (ValidateException $e) {
            return $this->error('验证失败', null, ['errors' => $e->getData()]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        $message = '更新个人资料成功';
        Session::set('success', $message);
        return $this->success($message, url('[user.read]', ['id' => $currentUser->id]));
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
