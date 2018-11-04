<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 04.11.18
 * Time: 15:04
 */

require_once __DIR__ . '/vendor/autoload.php';
use Routing\MatchedRoute;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

$config = parse_ini_file('config.ini', true);
date_default_timezone_set($config['Application']['timezone']);

try {

    $capsule = new Capsule;

    $capsule->addConnection(array(
        'driver'    => $config['Database']['driver'],
        'host'      => $config['Database']['host'],
        'database'  => $config['Database']['database'],
        'username'  => $config['Database']['username'],
        'password'  => $config['Database']['password'],
        'charset'   => $config['Database']['charset'],
        'collation' => $config['Database']['collation'],
        'prefix'    => $config['Database']['prefix'],
        'timezone'  => $config['Application']['timezone'],
    ));

// Set the event dispatcher used by Eloquent models... (optional)

    $capsule->setEventDispatcher(new Dispatcher(new Container));

// Make this Capsule instance available globally via static methods... (optional)
    $capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
    $capsule->bootEloquent();

    $smarty = new \Smarty();

    $smarty->setTemplateDir(__DIR__ . '/src/views');
    $smarty->setCompileDir('/tmp');
    $smarty->caching = false;

    $request = new \Routing\Request(array_merge($_SERVER, $_REQUEST));
    $router = new \http\Router($request);

    $router->add('/', '/', 'http\\controllers\\AppController:homeAction', 'GET');
    $router->add('create', '/', 'http\\controllers\\AppController:createShortLink', 'POST');
    $router->add('url', '/(id:any)', 'http\\controllers\\AppController:redirectAction');
    $router->add('all', '/all-links', 'http\\controllers\\AppController:getAllAction');
    $router->add('allPost', '/all-links', 'http\\controllers\\AppController:getAllAction', 'POST');
    $router->add('page', '/page/(id:num)', 'http\\controllers\\AppController:homeAction');

    $route = $router->match($request->getMethod(), $request->getPathInfo());

    if (null == $route) {
        $route = new MatchedRoute('http\\controllers\\AppController:error404Action');
    }

    list($class, $action) = explode(':', $route->getController(), 2);

    call_user_func_array(array(new $class($router, $smarty), $action), $route->getParameters());

} catch (Exception $e) {
    print $e->getMessage();
}
