<?php
declare (strict_types = 1);

namespace app\common\model;

use think\facade\Filesystem;

/**
 * @mixin think\Model
 */
class Upload
{

	/**
    * 保存上传图片
    * @Author   zhanghong(Laifuzi)
    * @param    File               $file         文件信息
    * @return   array
    */
    static public function saveImage($file): array
    {
        // 所有上传文件都保存在项目 public/storage/uploads 目录里
        $save_name = Filesystem::disk('public')->putFile('uploads', $file, 'md5');
        $save_name = str_replace("\\","/" , $save_name);
        //trace($save_name);
        return [
            'ext' => $file->extension(),
            // 文件实际存储在 public/storage 目录里
            'save_path' => '/thinkbbs/public/storage/'.$save_name,
            'sha1' => $file->hash("sha1"),
            'md5' => $file->hash("md5"),
            'size' => $file->getSize(),
            'origin_name' => $file->getOriginalName(),
        ];
    }
    //
}
