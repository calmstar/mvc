<?php
class UserModel extends Model {

    protected $table = 'user';  // 重载父类的属性，说明这个model使用user表

    public function getAllData () {
        return $this->all();
    }

}