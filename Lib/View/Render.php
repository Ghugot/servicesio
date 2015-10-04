<?php
/**
 * View : rendering class
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

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use \RuntimeException;
use \ReflectionClass;
use \Exception;

/**
 * The rendering class call the view class to collect Entity
 *
 * @category View
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Render
{
    /**
     * @var Container
     */
    private $_container;

    /**
     * the view classpath
     * 
     * @var string
     */
    private $_path;
    
    /**
     * the params to pass to the view
     * 
     * @var array
     */
    private $_params;

    /**
     * the views classes extension list
     *
     * @var array
     */
    private $_viewExtensionList = array();

    /**
     * constructor
     *
     * @param Container $container the Symfony Container
     * @param string $className the view class name
     * @param array  $params    the data to send to the view
     *
     * @return string
     */
    public function __construct(Container $container, $path, array $params = array())
    {
        $this->_container = $container;
        $this->_path = $path;
        $this->_params = $params;
        
        $this->_createViewExtensionList($path, $params);
    }
    
    public function get()
    {
        $content = end($this->_viewExtensionList)->content();
        
        $viewExtensionList = array_reverse($this->_viewExtensionList);
        foreach($viewExtensionList as $view) {
            $parser = new Parser($content, $view);
            $content = $parser->getReplacedContent();
        }

        return $content;
    }

    /**
     * get the view list based on parents hierarchy and associated with the path and params
     *
     * @param string $className the view class name
     * @param array  $params    the data to send to the view
     */
    private function _createViewExtensionList($path, array $params = array())
    {
        do {
            $className = $this->_extractClassNameFromPath($path);
            if (!class_exists($className)) {
                throw new Exception(
                    sprintf('path "%s" could not be found', $path)
                );
            }
    
            $reflexionObject = new ReflectionClass($className);
            $o = $reflexionObject->newInstance($this->_container);
            $o->setParams($params);
    
            $this->_viewExtensionList[] = $o;
    
            $path = $o->getParent();
        } while ($o->getParent());
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