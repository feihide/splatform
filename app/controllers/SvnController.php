<?php

use Phalcon\Tag as Tag;
use Phalcon\Flash as Flash;
use Phalcon\Session as Session;
use Phalcon\Mvc\Model\Criteria;
class SvnController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateAfter('main');
        Tag::setTitle('提交你的代码');
        parent::initialize();
    }

    //svn 提交文件清单
    public function indexAction()
    {

        $auth = $this->session->get('auth');
        $this->persistent->searchParams = null;
        $this->view->setVar("repos", array('jrd_branches'=>'jrd_branches','api_branches'=>'api_branches'));
        $this->view->setVar("section", array('本日','本周','本月'));

        $this->view->setVar("username", $auth['username']);
    }


    public function searchAction()
    {
        $auth = $this->session->get('auth');

        $numberPage = 1;
        $section  = 0;

        if ($this->request->isPost()) {
            $query =  $this->request->getPost('repos');
            $section = $this->request->getPost("section", "int");
            if ($section <= 0) {
                $section  = 0;
            }
            $this->persistent->searchParams = array('repo'=>$query,'section'=>$section);

        } else {
            $numberPage = $this->request->getQuery("page", "int");
            if ($numberPage <= 0) {
                $numberPage = 1;
            }

        }

        $arr = array(strtotime(date('Ymd',time())),mktime(0,0,0,date("m"),date("d")-date("w")+1,date("Y")),strtotime(date('YM',time())));

        $parameters = array();
        if ($this->persistent->searchParams) {
            $section = $this->persistent->searchParams['section'];
            $parameters = $this->persistent->searchParams;
            unset($parameters['section']);
        }
        $date=array('date'=>array('$gte'=>$arr[$section]));
        $cond= array_merge($date,$parameters,array('author'=>$auth['username']));


        $cond = array($cond,'sort'=>array('date'=>-1),'limit'=>50);

        $products = SvnLog::find($cond);

        if (count($products) == 0) {
            $this->flash->notice("暂无提交记录");
            return $this->forward("svn/index");
        }

//        $paginator = new Phalcon\Paginator\Adapter\Model(array(
//            "data" => $products,
//            "limit" => 10,
//            "page" => $numberPage
//        ));
//        $page = $paginator->getPaginate();
//
//        $this->view->setVar("page", $page);
          $this->view->setVar('repo',$this->persistent->searchParams['repo']);
          $this->view->setVar("products",$products);
    }

    /**
     * Edit the active user profile
     *
     */
    public function profileAction()
    {
        //Get session info
        $auth = $this->session->get('auth');

        //Query the active user
        $user = Users::findFirst($auth['id']);
        if ($user == false) {
            $this->_forward('index/index');
        }

        $request = $this->request;

        if (!$request->isPost()) {
            Tag::setDefault('name', $user->name);
            Tag::setDefault('email', $user->email);
        } else {

            $name = $request->getPost('name', 'string');
            $email = $request->getPost('email', 'email');

            $name = strip_tags($name);

            $user->name = $name;
            $user->email = $email;
            if ($user->save() == false) {
                foreach ($user->getMessages() as $message) {
                    $this->flash->error((string) $message);
                }
            } else {
                $this->flash->success('Your profile information was updated successfully');
            }
        }
    }
}
