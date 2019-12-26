<?php
declare (strict_types = 1);

namespace app\index\controller;

use think\Request;
use think\facade\Session;
use app\common\model\Category as CategoryModel;
use app\common\exception\ValidateException;
use app\common\model\Topic as TopicModel;
use app\common\model\Reply as ReplyModel;

class Topic extends Base
{
    protected $middleware = [
        'auth' => ['except' => ['index','read']],
    ];

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $param = $this->request->only(['order'],'get');
        return $this->fetch('index', [
         //   'paginate' => TopicModel::paginate(20),
         'paginate'     => TopicModel::minePaginate($param),   //预载入分页查询
        ]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
         return $this->fetch('form', [
            'categories' => CategoryModel::select(),
            'topic' => [],
        ]);
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
            Session::flash('danger', $message);
            return $this->redirect('[topic.create]');
        }

        $message = null;
        try {
            $data = $this->request->post();
            $topic = TopicModel::createItem($data);
        } catch (ValidateException $e) {
            return $this->error($e->getMessage(), null, ['errors' => $e->getData()]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        $message = '话题创建成功。';
        Session::flash('success', $message);
        //return $this->success($message, '[topic.index]');
        return $this->success($message,url('[topic.read]',['id'=>$topic->id]));
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
       
        // 关联创建话题用户信息查询
        $topic = TopicModel::with(['user' => function($query) {
            $query->field('id, name, avatar');
        }])->find($id);



        if (empty($topic)) {
            return $this->redirect('[topic.index]');
        }

        // 浏览次数加 1
        $topic->view_count += 1;
        $topic->save();

        // 用话题的摘要信息覆盖已有的SEO信息
        $this->site['description'] = $topic->excerpt;

        return $this->fetch('topic/read', [
          'topic' => $topic,
          'site' => $this->site,
          'reply_paginate' => ReplyModel::minePaginate(['topic_id' => $topic->id]),
        ]);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $message = null;
        $topic = TopicModel::find($id);

        if (empty($topic)) {
            $message = '对不起，编辑话题不存在。';
        } else if (!$topic->canUpdate()) {
            $message = '对不起，您没有权限编辑该话题。';
        }

        if (!empty($message)) {
            Session::set('danger', $message);
            return $this->redirect('[topic.index]');
        }

        return $this->fetch('topic/form', [
            'topic' => $topic,
            'categories' => CategoryModel::select(),
        ]);
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
        $message = null;
        $topic = TopicModel::find($id);

        if (empty($topic)) {
            $message = '对不起，编辑话题不存在。';
        } else if (!$topic->canUpdate()) {
            $message = '对不起，您没有权限编辑该话题。';
        } else if (!$this->request->isPut() || !$this->request->isAjax()) {
            $message = '对不起，您访问的页面不存在。';
        }

        if (!empty($message)) {
            Session::set('danger', $message);
            return $this->error($message, '[topic.index]');
        }

        try {
            $data = $this->request->post();
            $topic->updateInfo($data);
        } catch (ValidateException $e) {
            return $this->error($e->getMessage(), null, ['errors' => $e->getData()]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        $message = '话题更新成功。';
        Session::set('success', $message);
        return $this->success($message, url('[topic.read]', ['id' => $topic->id]));
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $message = null;
        $topic = TopicModel::find($id);

        if (empty($topic)) {
            return $this->error('删除话题不存在', '[topic.index]');
        } else if (!$topic->canDelete()) {
            return $this->error('对不起，您没有权限删除该话题', '[topic.index]');
        }

        $topic->delete();

        $message = '删除成功';
        Session::set('success', $message);
        return $this->success($message, '[topic.index]');
        }
}
