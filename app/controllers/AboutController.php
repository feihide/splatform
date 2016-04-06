<?php

class AboutController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateAfter('main');
        Phalcon\Tag::setTitle('关于SVN平台');
        parent::initialize();
    }

    public function indexAction()
    {
    }
}
