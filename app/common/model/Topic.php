<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\Paginator;
use app\common\validate\Topic as Validate;
use app\common\exception\ValidateException;
use think\helper\Str;

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

    /**
     * 范围查询-最近回回复排序
     * @Author   zhanghong(Laifuzi)
     * @param    Query             $query 查询构建器
     * @return   Query                    查询构建器
     */
    public static function scopeRecentReplied($query)
    {
        // 按最后更新时间排序
        return $query->order('update_time', 'DESC');
    }

    /**
     * 范围查询-最新发表
     * @Author   zhanghong(Laifuzi)
     * @param    Query             $query 查询构建器
     * @return   Query                    查询构建器
     */
    public static function scopeRecent($query)
    {
        // 按照创建时间排序
        return $query->order('id', 'DESC');
    }

    /**
     * 范围查询-排序方式
     * @Author   zhanghong(Laifuzi)
     * @param    Query              $query      查询构建器
     * @param    string             $order_type 排序方式
     * @return   Query                          查询构建器
     */
    public static function scopeWithOrder($query, $order_type)
    {
        switch ($order_type) {
            case 'recent':
                $query->recent();
                break;
            default:
                // 默认按最后回复降序排列
                $query->recentReplied();
                break;
        }

        return $query->with(['user', 'category']);
    }

    /**
     * 分页查询方法
     * @Author   zhanghong(Laifuzi)
     * @param    array              $params    请求参数
     * @param    int                $page_rows 每页显示数量
     * @return   Paginator
     */
    public static function minePaginate(array $param = [], int $per_page = 20): Paginator
    {
       // return static::with(['user', 'category'])->paginate($per_page);
       	//$static = static::with(['user', 'category']);
       	
       	$order_type = NULL;
        if (isset($param['order'])) {
            $order_type = $param['order'];
        }
        $static = static::withOrder($order_type);

    	foreach ($param as $name => $value) {
        	if (empty($value)) {
            	continue;
        	}
        	switch ($name) {
        		case 'user_id':
            	case 'category_id':
                	$static = $static->where($name, intval($value));
                	break;
        	}
    	}

    	return $static->paginate($per_page);
    }

    /**
     * 创建记录
     * @Author   zhanghong(Laifuzi)
     * @param    array              $data 表单提交数据
     * @return   Topic
     */
    public static function createItem(array $data): Topic
    {
        $validate = new Validate;
        if (!$validate->batch(true)->check($data)) {
            $e = new ValidateException('数据验证失败');
            $e->setData($validate->getError());
            throw $e;
        }

        $data['user_id'] = 0;
        $current_user = User::currentUser();
        if (!empty($current_user)) {
            // 如果登录用户存在时，user_id 等于当前登录用户ID
            $data['user_id'] = $current_user->id;
        }

        try {
            $topic = new static;
            $topic->allowField(['title', 'category_id', 'body', 'user_id','excerpt'])->save($data);
        } catch (\Exception $e) {
            throw new \Exception('创建话题失败');
        }

        return $topic;
    }

    /**
     * 话题写入事件
     * @Author   zhanghong(Laifuzi)
     * @param    Topic              $topic 话题实例
     * @return   bool
     */
    public static function onBeforeWrite(Topic $topic)
    {
        $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($topic->body)));
        $topic->excerpt = Str::substr($excerpt, 0, 200);
        return true;
    }

    /**
     * 是否可以编辑记录
     * @Author   zhanghong(Laifuzi)
     * @return   bool
     */
    public function canUpdate(): bool
    {
        $current_user = User::currentUser();
        if (empty($current_user)) {
            return false;
        } else if ($this->user_id != $current_user->id) {
            return false;
        }
        return true;
    }

    /**
     * 登录用户是否可以删除记录
     * @Author   zhanghong(Laifuzi)
     * @return   bool
     */
    public function canDelete(): bool
    {
        $current_user = User::currentUser();
        if (empty($current_user)) {
            return false;
        } else if ($this->user_id != $current_user->id) {
            return false;
        }
        return true;
    }

    /**
     * 更新记录
     * @Author   zhanghong(Laifuzi)
     * @param    array              $data 表单提交数据
     * @return   Topic
     */
    public function updateInfo(array $data): Topic
    {
        $validate = new Validate;
        if (!$validate->batch(true)->check($data)) {
            $e = new ValidateException('数据验证失败');
            $e->setData($validate->getError());
            throw $e;
        }

        $this->allowField(['title', 'category_id', 'body', 'excerpt'])->save($data);
        return $this;
    }
}
