<?php
class IndexController extends Controller {
    /**
     * 实例化该类的时候先实例化父类，父类控制器判断__auto方法若存在，则调用
     * 避免 parent::__construct 使用过多
     * 直接继承Controller使用 __init()，间接继承使用 __auto
     */
    public function __init() {

    }

    public function __empty() {
        echo 'action empty';
    }

    public function index() {
//        $m = new Model('user');
//        $sql = "select * from jt_user limit 1";
//        p($m->query($sql));
//        p(M('user')->field('name,email')->order('id desc')->limit(1)->all());
//        p(M('user')->all());
//        p(M('user')->exe("INSERT INTO `jt_user` VALUES ('2', '494025451222@qq.com', '$2y$10$/tsKYctqQKNZt8B9RfvanulZtp02zcoppQiRxCGDjJOV5LejbmAle', '陈文11', '1501586745', 0, '17875511965', '', 1, '192.168.79.41', '1526553918')") );
//        p(M('user')->where('id=34')->delete());
//        if(IS_POST) {
//            unset($_POST['submit']);
//            $_POST['rgdate'] = 20000000;
//            $_POST['role_id'] = 2;
//
////            $id = M('user')->add();
////            $this->success($id. ' 添加成功');
//            $res = M('user')->where('id=35')->update();
//            $this->success($res. ' 成功');
//        }
//        $this->display();

//        $user = new UserModel();
//        p($user->getAllData());

//        p( K('User')->getAllData() );

        // 模板缓存失效，才进行重新分配
        if (!$this->isCached() ) {
            $this->assign('var', time());
        }
        $this->display();

    }

}