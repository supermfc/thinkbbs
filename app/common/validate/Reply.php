<?php
declare (strict_types = 1);

namespace app\common\validate;

use think\Validate;

class Reply extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'topic_id' => 'require|gt:0',
        'content' => 'require|length:3,200',
    ];

    protected $message = [
        'topic_id.require' => '所属话题不能为空',
        'topic_id.gt' => '所属话题不存在',
        'content.require' => '回复内容不能为空',
        'content.length' => '标题长度必须在3-200个字符之间',
    ];
}
