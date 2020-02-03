<?php


class Router
{
    private $routes;
    public function __construct()
    {
        $routesPath = ROOT . '/config/routes.php';
        $this->routes = include_once($routesPath);
    }

    private function getUri()
    {
        if(!empty($_SERVER['REQUEST_URI'])){
            return trim($_SERVER['REQUEST_URI'], '/');
        }
    }

    public function run()
    {
        //
        $uri = $this->getUri();

        //
        foreach($this->routes as $uriPattern=>$path){
            //
            if(preg_match("~$uriPattern~", $uri)){
                // echo "<br>Где ищем (запрос, который набрал пользователь): ".$uri;
                // echo "<br>Что ищем (совпадение из правила): ".$uriPattern;
                // echo "<br>Кто обрабатывает: ".$path; */
                $path = preg_replace("~$uriPattern~", $path, $uri);

                $segments = explode('/', $path);

                //
                $controllerName = ucfirst(array_shift($segments)) . 'Controller';
                $actionName = 'action' . ucfirst(array_shift($segments));
                $parameters = $segments;


                //
                $controllerFile = ROOT . '/controllers/' . $controllerName . '.php';
                if(file_exists($controllerFile)){
                    require_once($controllerFile);
                }

                //
                $controllerObject = new $controllerName();
                $result = call_user_func_array(array($controllerObject, $actionName), $parameters);

                if($result != null){
                    break;
                }

            }
        }
    }


}