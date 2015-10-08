<?php
/**
 * View : service accessor
 *
 * PHP Version 5
 *
 * @category View
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */

namespace Redgem\ServicesIOBundle\Lib\View;

use Redgem\ServicesIOBundle\Lib\Factory\Output\Factory;
use Redgem\ServicesIOBundle\Lib\View\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * the Service class drive the rendering of dataflow and is called by controllers
 *
 * @category View
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Service
{
    /**
     * @var Container
     */
    private $_container;

    /**
     * the constructor
     *
     * @param Container $container the Symfony Container
     */
    public function __construct(Container $container)
    {
        $this->_container = $container;
    }

    /**
     * render the view. This method is supposed to be called by the controller
     * 
     * @param string $viewpath  the viewpath (a string like MyBundle:MessageView)
     * @param array  $params    the data to send to the view
     *
     * @return string
     */
    public function render($viewpath, array $params = array())
    {
        $render = new Render($this->_container, $viewpath, $params);
        $node = $render->get();

        $factory = new Factory($node);

        $response = new Response();
        $response
            ->setSource($node)
            ->setContent(
                json_encode($factory->get())
            );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}