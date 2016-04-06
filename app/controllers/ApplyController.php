<?php

use Phalcon\Tag as Tag;
use Phalcon\Flash as Flash;
use Phalcon\Session as Session;

class ApplyController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateAfter('main');
        Tag::setTitle('申请栏');
        parent::initialize();
    }

    public function newAction(){
        if ($this->request->isPost()) {
            $apply = new Apply();
            $auth = $this->session->get('auth');
            $apply->repo = $this->request->getPost('repo');
            $apply->revision = join(',', $this->request->getPost('revision'));
            $apply->content =  strip_tags($this->request->getPost('content'));
            $apply->user_id = $auth['id'];

            $apply->apply_name = $auth['name'];
            $apply->status = 0;
            $apply->apply_time = time();
            //根据ID获取版本详情
            $detail=array();
            foreach($this->request->getPost('revision') as $item){
                $log = SvnLog::findById($item);
                //监测文件是否已经提交
                if(isset($log->isApply) && $log->isApply==1){
                    $this->flash->notice("包含已提交的版本");
                    return $this->forward("svn/index");
                }
                //如果没，则修改文件状态
                $log->isApply=1;
                $log->save();

                array_push($detail,array('revision'=>$log->revision,'files'=>$log->files));
            }

            $apply->detail = json_encode($detail);
            if ($apply->save() == false) {
                foreach ($apply->getMessages() as $message) {
                    $this->flash->error((string) $message);
                }
            } else {
                $this->flash->success('申请完成');
                return $this->forward('apply/index');
            }

        }
        else{
            $this->flash->notice("错误的请求，请重试");
            return $this->forward("svn/index");
        }
    }


    //申请列表
    public function indexAction()
    {
        //该页面，三个角色公用
        //开发人员可以看到自己申请的列表清单
        //测试人员看到所有申请的清单
        //运维只看见所有已审核通过清单s
        $auth = $this->session->get('auth');
        switch($auth['role']){
            case 'dev':
                $param = "user_id={$auth['id']}";
                break;
            case 'oper':
                $param = "status=1";
                break;
            default:
                $param="";

        }


        $numberPage = 1;

        $numberPage = $this->request->getQuery("page", "int");
        if ($numberPage <= 0) {
            $numberPage = 1;
        }

        $cond = array($param,'order'=>'apply_time desc');

        $products = Apply::find($cond);

        if (count($products) == 0) {
            $this->flash->notice("暂无提交记录");
            //return $this->forward("svn/index");
        }

        $paginator = new Phalcon\Paginator\Adapter\Model(array(
            "data" => $products,
            "limit" => 10,
            "page" => $numberPage
        ));
        $page = $paginator->getPaginate();

        $this->view->setVar('role',$auth['role']);
        $this->view->setVar("page", $page);

    }

    //审核通过
    public function checkAction($id){
        $request = $this->request;
        if (!$request->isPost()) {

            $id = $this->filter->sanitize($id, array("int"));
            $auth = $this->session->get('auth');
            $products = Apply::findFirst("id='$id'");

            if ($products == false) {
                $this->flash->error("该申请不存在 ".$id);
                return $this->forward("apply/index");
            }
            if($products->status!=0){
                $this->flash->error("该申请不存在 ".$id);
                return $this->forward("apply/index");
            }

            $products->check_id = $auth['id'];
            $products->check_name = $auth['name'];
            $products->check_time = time();
            $products->status =1;
            if (!$products->save()) {
                foreach ($products->getMessages() as $message) {
                    $this->flash->error((string) $message);
                }
                $this->flash->success("操作失败，请重试");
                return $this->forward("apply/index");
            } else {
                $this->flash->success("已提交上线");
                return $this->forward("apply/index");
            }
        }

    }

//审核失败
    public function uncheckAction($id){
        $request = $this->request;
        if (!$request->isPost()) {

            $id = $this->filter->sanitize($id, array("int"));
            $auth = $this->session->get('auth');
            $products = Apply::findFirst("id='$id'");

            if ($products == false) {
                $this->flash->error("该申请不存在 ".$id);
                return $this->forward("apply/index");
            }
            if($products->status!=0){
                $this->flash->error("该申请不存在 ".$id);
                return $this->forward("apply/index");
            }

            $products->check_id = $auth['id'];
            $products->check_name = $auth['name'];
            $products->check_time = time();
            $products->status =2;
            if (!$products->save()) {
                foreach ($products->getMessages() as $message) {
                    $this->flash->error((string) $message);
                }
                $this->flash->success("操作失败，请重试");
                return $this->forward("apply/index");
            } else {
                $this->flash->success("已驳回");
                return $this->forward("apply/index");
            }
        }

    }

    //发布
    public function publishAction($id){
        $request = $this->request;
        if (!$request->isPost()) {

            $id = $this->filter->sanitize($id, array("int"));
            $auth = $this->session->get('auth');
            $products = Apply::findFirst("id='$id'");

            if ($products == false) {
                $this->flash->error("该申请不存在 ".$id);
                return $this->forward("apply/index");
            }
            if($products->status!=1){
                $this->flash->error("该申请未通过测试 ".$id);
                return $this->forward("apply/index");
            }

            $products->publish_id = $auth['id'];
            $products->publish_name = $auth['name'];
            $products->publish_time = time();
            $products->status =3;
            if (!$products->save()) {
                foreach ($products->getMessages() as $message) {
                    $this->flash->error((string) $message);
                }
                $this->flash->success("操作失败，请重试");
                return $this->forward("apply/index");
            } else {
                $this->flash->success("上线成功");
                return $this->forward("apply/index");
            }
        }

    }


}
