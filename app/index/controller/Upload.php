<?php
declare (strict_types = 1);

namespace app\index\controller;

use think\Request;
use app\common\model\Upload as UploadModel;

class Upload extends Base
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
        return $this->save_image();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        return $this->save_image();
    }


    private function save_image()
    {
        $backcall = $this->request->param('backcall');

        $width = $this->request->param('width',100);
        $height = $this->request->param('height',100);
        //当前图片路径
        $image = $this->request->param('image');
        // 错误信息
        $error_msg = '';

        if ($this->request->isPost()) {
            $file = $this->request->file('image');
            try{
                $upload_info = UploadModel::saveImage($file);

                $image = $upload_info['save_path'];   
            } catch(ValidateException $e) {
                $errors = $e->getData();
                $error_msg = $errors['file'];
            }
            
        } else {
            $image = $this->request->param('image');
        }

        return $this->fetch('create',[
            'backcall' => $backcall,
            'width'     => $width,
            'height'    =>  $height,
            'image'     =>  $image,
            'error_msg' => $error_msg,
        ]);
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
