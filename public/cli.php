<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-9-9
 * Time: 下午7:26
 */

 use Phalcon\DI\FactoryDefault\CLI as CliDI,
     Phalcon\CLI\Console as ConsoleApp;

 define('VERSION', '1.0.0');

 //Using the CLI factory default services container
 $di = new CliDI();

$di->set('mongo', function() {
    $mongo = new MongoClient('192.168.1.235:27017');
    return $mongo->selectDB("log");
}, true);

 // Define path to application directory
 defined('APPLICATION_PATH')
 || define('APPLICATION_PATH', realpath(dirname(dirname(__FILE__))).'/app');

 /**
  * Register the autoloader and tell it to register the tasks directory
  */
 $loader = new \Phalcon\Loader();

$config = new Phalcon\Config\Adapter\Ini(__DIR__ . '/../app/config/config.ini');
/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    array(
        APPLICATION_PATH . '/tasks',
        __DIR__ . $config->application->controllersDir,
        __DIR__ . $config->application->pluginsDir,
        __DIR__ . $config->application->libraryDir,
        __DIR__ . $config->application->modelsDir,
    )
)->register();


 // Load the configuration file (if any)
 if(is_readable(APPLICATION_PATH . '/config/config.php')) {
     $config = include APPLICATION_PATH . '/config/config.php';
     $di->set('config', $config);
 }

//$di->set('collectionManager', function(){
//    return new Phalcon\Mvc\Collection\Manager();
//}, true);

//Registering the collectionManager service
$di->set('collectionManager', function() {

    $eventsManager = new Phalcon\Events\Manager();

    // Attach an anonymous function as a listener for "model" events
    $eventsManager->attach('collection', function($event, $model) {
        if (get_class($model) == 'Robots') {
            if ($event->getType() == 'beforeSave') {
                if ($model->name == 'Scooby Doo') {
                    echo "Scooby Doo isn't a robot!";
                    return false;
                }
            }
        }
        return true;
    });

    // Setting a default EventsManager
    $modelsManager = new Phalcon\Mvc\Collection\Manager();
    $modelsManager->setEventsManager($eventsManager);
    return $modelsManager;

}, true);


 //Create a console application
 $console = new ConsoleApp();
 $console->setDI($di);

 /**
  * Process the console arguments
  */
 $arguments = array();
 foreach($argv as $k => $arg) {
     if($k == 1) {
         $arguments['task'] = $arg;
     } elseif($k == 2) {
         $arguments['action'] = $arg;
     } elseif($k >= 3) {
         $arguments['params'][] = $arg;
     }
 }

 // define global constants for the current task and action
 define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
 define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

 try {
     // handle incoming arguments
     $console->handle($arguments);
 }
 catch (\Phalcon\Exception $e) {
     echo $e->getMessage();
     exit(255);
 }