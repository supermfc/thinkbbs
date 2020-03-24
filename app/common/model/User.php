<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\facade\Session;
use app\common\validate\User as Validate;
use app\common\exception\ValidateException;
use app\common\validate\Login as LoginValidate;

/**
 * @mixin think\Model
 */
class User extends Model
{
    public const CURRENT_KEY = 'current_user';

    protected const ACTIVE_CACHE_KEY = 'active_users';
    protected const ACTIVE_CACHE_SECONDS = 65*60;
    protected const ACTIVE_LIMIT_NUMBER = 6;   // 取出来多少用户

    protected const TOPIC_WEIGHT = 4;  // 话题权重
    protected const REPLY_WEIGHT = 1;  // 回复权重
    protected const PASS_DAYS = 7;     // 多少天内发表过内容

    // 计算活跃用户 Trait
    use helper\ActiveUser;


    public $dateFormat = '\app\common\model\DateFormat';


    /**
     * 验证字段值是否唯一
     * @Author   zhanghong(Laifuzi)
     * @param    array              $data 验证字段和字段值
     * @param    int                $id   用户ID
     * @return   bool
     */
    public static function checkFieldUnique(array $data, int $id = 0): bool
    {
        $field_name = null;
        // 验证字段名必须存在
        if (!isset($data['field'])) {
            return false;
        }
        // 验证字段名，对应数据库字段
        $field_name = $data['field'];

        // 验证字段值必须存在
        if (!isset($data[$field_name])) {
            return false;
        }
        $field_value = $data[$field_name];

        $query = static::where($field_name, $field_value);
        if ($id > 0) {
            $query->where('id', '<>', $id);
        }

        //如果数据库中存在记录
        if ($query->count()) {
            return false;
        }

        return true;
    }

    /**
     * 注册新用户
     * @Author   zhanghong(Laifuzi)
     * @param    array              $data 表单提交数据
     * @return   User                     新注册用户信息
     */
    public static function register(array $data): User
    {
        $validate = new Validate;
        if (!$validate->scene('form_register')->batch(true)->check($data)) {
            $e = new ValidateException('注册数据验证失败');
            $e->setData($validate->getError());
            throw $e;
        }

        try {
            $user = new self;
            $user->allowField(['name', 'mobile', 'password'])->save($data);
        } catch (\Exception $e) {
            throw new \Exception('创建用户失败');
        }

        return $user;
    }

    /**
     * 密码保存时进行加密
     * @Author   zhanghong(Laifuzi)
     * @param    string             $value 原始密码
     */
    public function setPasswordAttr(string $value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }


    /**
     * 用户登录
     * @Author   zhanghong(Laifuzi)
     * @param    string             $mobile   登录手机号码
     * @param    string             $password 登录密码
     * @return   User
     */
    public static function login(string $mobile, string $password): User
    {
        $errors = [];

        $validate = new LoginValidate;
        $data = ['mobile' => $mobile, 'password' => $password];
        if (!$validate->batch(true)->check($data)) {
            $e = new ValidateException('登录数据验证失败');
            $e->setData($validate->getError());
            throw $e;
        }

        $user = self::where('mobile', $mobile)
                    ->find();       //find返回的结果是对象而不是数组

        if (empty($user)) {
            // 传输注册手机号码不存在
            $errors['mobile'] = '注册用户不存在';
        } else if (!password_verify($password, $user->password)) {
            // 传输登录密码错误
            $errors['mobile'] = '登录手机或密码错误';
        }

        if (!empty($errors)) {
            $e = new ValidateException('登录数据验证失败');
            $e->setData($errors);
            throw $e;
        }

        // 把去除登录密码以外的信息存储到 Session 里
        unset($user['password']);
        Session::set(self::CURRENT_KEY, $user);

        return $user;
    }

    /**
     * 当前登录用户
     * @Author   zhanghong(Laifuzi)
     * @return   User
     */
    public static function currentUser()
    {
        return Session::get(self::CURRENT_KEY);
    }

    /**
     * 退出登录
     * @Author   zhanghong(Laifuzi)
     * @return   bool
     */
    public static function logout(): bool
    {
        Session::delete(self::CURRENT_KEY);
        return true;
    }

    /**
     * 重置密码
     * @Author   zhanghong(Laifuzi)
     * @param    array              $data 表单提交数据
     * @return   bool
     */
    public static function resetPassword(array $data): bool
    {
        if (!isset($data['mobile'])) {
            $e = new ValidateException('重置密码验证失败');
            $e->setData(['mobile' => '注册手机号码不能为空']);
            throw $e;
        }

        $user = self::where('mobile', $data['mobile'])->find();
        if (empty($user)) {
            $e = new ValidateException('重置密码验证失败');
            $e->setData(['mobile' => '注册手机号码不存在']);
            throw $e;
        }

        $validate = new Validate;
        if (!$validate->batch(true)->scene('reset_password')->check($data)) {
            $e = new ValidateException('重置密码验证失败');
            $e->setData($validate->getError());
            throw $e;
        }

        $user->password = $data['password'];
        $is_save = $user->save();
        if (!$is_save) {
            throw new \Exception('重置密码失败');
        }
        return true;
    }

    /**
     * 更新个人资料
     * @Author   zhanghong(Laifuzi)
     * @param    array              $data 更新数据
     * @return   bool
     */
    public function updateProfile(array $data): bool
    {
        $validate = new Validate;
        if (!$validate->batch(true)->scene('update_profile')->check($data)) {
            $e = new ValidateException('更新个人信息失败');
            $e->setData($validate->getError());
            throw $e;
        }

        $is_save = $this->allowField(['name', 'introduction','avatar'])->save($data);
        if (!$is_save) {
            throw new \Exception('更新个人信息失败');
        }

        // 刷新用户信息
        $user = $this->refresh();
        unset($user['password']);
        Session::set(self::CURRENT_KEY, $user);

        return true;
    }

    /**
     * 用户头像路径
     * @Author   zhanghong(Laifuzi)
     * @return   string
     */
    public function getAvatarPathAttr()
    {
        //如果用户没有上传头像，那么获取默认的头像
        if (empty($this->avatar)) {
            return '/thinkbbs/public/static/assets/index/images/default_avatar.png';
        }
        return $this->avatar;
    }

    /**
     * 是否是实例对象的作者
     * @Author   zhanghong(Laifuzi)
     * @param    Object             $item 实例对象
     * @return   bool
     */
    public function isAuthorOf($item): bool
    {
        if ($this->id == $item->user_id) {
            return true;
        }

        return false;
    }
}
