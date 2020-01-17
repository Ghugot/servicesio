<?php
/**
 * Model : Listener
 *
 * PHP Version 5
 *
 * @category Model
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */

namespace Redgem\ServicesIOBundle\Lib\Model;

use Redgem\ServicesIOBundle\Lib\Factory\Input\Factory;
use Redgem\ServicesIOBundle\Lib\Model\Service as Model;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use \Exception;

/**
 * Called before the controller, the Listener update on the fly the 
 * Request object to hydrate the selected Model with Request body
 * and make it accessible.
 *
 * @category Model
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Listener
{
    /**
     * the servicesio_model service
     * 
     * @var Model
     */
    private $_model;

    /**
     * the router service
     *
     * @var Router
     */
    private $_router;
    

    /**
     * the constructor
     * 
     * @param Model $model
     */
    public function __construct(Model $model, Router $router)
    {
        $this->_model = $model;
        $this->_router = $router;
        
    }

    /**
     * Catch the request content and turn it into the
     * configured model on route defaults parameters
     * 
     * @param FilterControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event)
    {
        try {
            $route = $this
                ->_router
                ->match(
                    $event->getRequest()->getPathInfo()
                );

            if (!array_key_exists('servicesio_model', $route)) {
                return;
            }

            $event->getRequest()->model
                = $this->_model->build(
                    $event->getRequest()->getContent(),
                    $route['servicesio_model']
                );

        } catch (Exception $e) {}
    }
}
