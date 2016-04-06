<?php

use Phalcon\Events\Event,
	Phalcon\Mvc\User\Plugin,
	Phalcon\Mvc\Dispatcher,
	Phalcon\Acl;

/**
 * Security
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */
class Security extends Plugin
{

	public function __construct($dependencyInjector)
	{
		$this->_dependencyInjector = $dependencyInjector;
	}

	public function getAcl()
	{
		if (!isset($this->persistent->acl)|| 1) {

			$acl = new Phalcon\Acl\Adapter\Memory();

			$acl->setDefaultAction(Phalcon\Acl::DENY);

			//Register roles
			$roles = array(
				'dev' => new Phalcon\Acl\Role('Devs'),
                'test' => new Phalcon\Acl\Role('Tests'),
                'oper' => new Phalcon\Acl\Role('Opers'),
                'admin' => new Phalcon\Acl\Role('Admins'),
				'guest' => new Phalcon\Acl\Role('Guests')
			);
			foreach ($roles as $role) {
				$acl->addRole($role);
			}

			//Private area resources

            $adminResources = array(
                'session' => array('register','start'),
            );
            foreach ($adminResources as $resource => $actions) {
                $acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
            }

			$devResources = array(
				'apply' => array('index', 'new', 'edit', 'delete'),
				'svn' => array('index', 'search'),
			);
			foreach ($devResources as $resource => $actions) {
				$acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
			}


            $testResources = array(
                'apply' => array('index','check','uncheck'),
            );
            foreach ($testResources as $resource => $actions) {
                $acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
            }

            $operResources = array(
                'apply'=>array('index','publish'),
            );
            foreach ($operResources as $resource => $actions) {
                $acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
            }


			//Public area resources
			$publicResources = array(
				'index' => array('index'),
				'about' => array('index'),
				'session' => array('index','end','start'),
				'contact' => array('index', 'send'),
                'invoices' => array('index', 'new', 'edit', 'save', 'create', 'delete'),
                'products' => array('index', 'search', 'new', 'edit', 'save', 'create', 'delete'),
                'producttypes' => array('index', 'search', 'new', 'edit', 'save', 'create', 'delete'),

			);
			foreach ($publicResources as $resource => $actions) {
				$acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
			}

            //绑定角色
            foreach ($adminResources as $resource => $actions) {
                foreach ($actions as $action){
                    $acl->allow('Admins', $resource, $action);
                }
            }


			//Grant acess to private area to role Users
			foreach ($devResources as $resource => $actions) {
				foreach ($actions as $action){
					$acl->allow('Devs', $resource, $action);
				}
			}

            foreach ($testResources as $resource => $actions) {
                foreach ($actions as $action){
                    $acl->allow('Tests', $resource, $action);
                }
            }

            foreach ($operResources as $resource => $actions) {
                foreach ($actions as $action){
                    $acl->allow('Opers', $resource, $action);
                }
            }


//Grant access to public areas to both users and guests
            foreach ($roles as $role) {
                foreach ($publicResources as $resource => $actions) {
                    foreach ($actions as $action){
                        $acl->allow($role->getName(), $resource, $action);
                    }
                }
            }
			//The acl is stored in session, APC would be useful here too
			$this->persistent->acl = $acl;
		}

		return $this->persistent->acl;
	}

	/**
	 * This action is executed before execute any action in the application
	 */
	public function beforeDispatch(Event $event, Dispatcher $dispatcher)
	{

		$auth = $this->session->get('auth');
		if (!$auth){
			$role = 'Guests';
		} else {
			$role = ucfirst($auth['role']).'s';
		}

		$controller = $dispatcher->getControllerName();
		$action = $dispatcher->getActionName();

		$acl = $this->getAcl();

		$allowed = $acl->isAllowed($role, $controller, $action);

        if ($allowed != Acl::ALLOW) {
			$this->flash->error("您没有权限进入该模块");
			$dispatcher->forward(
				array(
					'controller' => 'index',
					'action' => 'index'
				)
			);
			return false;
		}

	}

}
