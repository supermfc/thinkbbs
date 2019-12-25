<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Topic extends Model
{
    public function user() 
    {
    	return $this->belongsTo('User');
    }

    public function category()
    {
    	return $this->belongsTo('Category');
    }
}
