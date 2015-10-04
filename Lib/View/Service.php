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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use \RuntimeException;
use \ReflectionClass;

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
     * @param string $className the view class name
     * @param array  $params    the data to send to the view
     *
     * @return string
     */
    public function render($path, array $params = array())
    {
        $className = $this->_extractClassNameFromPath($path);
        if (!class_exists($className)) {
            throw new Exception(
                sprintf('path "%s" could not be found', $path)
            );
        }

        $reflexionObject = new ReflectionClass($className);
        $o = $reflexionObject->newInstance($this->_container);
        $o->setParams($params);

        $response = new Response();
        $factory = new Factory($o->execute());

        $response->setContent(json_encode($factory->get()));
        $response->headers->set('Content-Type', 'application/json');
        return $response;      
    }

    /**
     * get view classname from path
     * 
     * @param string $path
     * 
     * @return string
     */
    private function _extractClassNameFromPath($path)
    {
        $path = preg_replace('|^([^:]+):(.+)$|Ui', '$1:View:$2', $path);
        return str_replace(':', '\\', $path);
    }
}