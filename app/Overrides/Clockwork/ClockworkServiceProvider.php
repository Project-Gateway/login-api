<?php
namespace App\Overrides\Clockwork;

class ClockworkServiceProvider extends \Clockwork\Support\Lumen\ClockworkServiceProvider
{
    public function registerWebRoutes()
    {
        parent::registerWebRoutes();

        $router = isset($this->app->router) ? $this->app->router : $this->app;

        $router->get('/__clockwork', 'App\Overrides\Clockwork\Controller@webRedirect');
    }
}
