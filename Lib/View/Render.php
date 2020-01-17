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
 * The rendering class call the view class to collect the different nodes and build a final tree.
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
     * @param Container            $container the Symfony Container
     * @param string|array<string> $viewpath the viewpath (a string like MyBundle:MessageView)
     * @param array                $params    the data to send to the view
     *
     * @return string
     */
    public function __construct(Container $container, $viewpath, array $params = array())
    {
        $this->_container = $container;
        $this->_params = $params;
        
        $this->_createViewExtensionList($viewpath, $params);
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
     * @param string $viewpath  the viewpath (a string like MyBundle:MessageView)
     * @param array  $params    the data to send to the view
     */
    private function _createViewExtensionList($viewpath, array $params = array())
    {
        do {
            $reflexionObject = new ReflectionClass($this->_getClassNameFromConfig($viewpath));
            $o = $reflexionObject->newInstance($this->_container);
            $o->setParams($params);

            if ($o->content() && $o->getParent()) {
                throw new Exception(
                    sprintf(
                        'class "%s" implentent both content() and getParent() that is not allowed.',
                        get_class($o)
                    )
                );
            }

            $this->_viewExtensionList[] = $o;
    
            $viewpath = $o->getParent();
        } while ($o->getParent());
    }

    /**
     * get the classname from a configuration
     * 
     * @param string|array<string> $viewpath the viewpath (a string like MyBundle:MessageView)
     * 
     * @return string
     */
    private function _getClassNameFromConfig($viewpath)
    {
        $list = $this->_getClassListFromPath($viewpath);

        foreach($list as $item) {
            if (class_exists($item)) {
                return $item;
            }
        }

        throw new Exception(
            is_array($viewpath) ? sprintf('None of "%s" view path could be found.', implode('", "', $viewpath))
                : sprintf('View path "%s" could not be found.', $viewpath)
        );
    }

    /**
     * this method extract a list of view classes that match a path
     * according to path values (array or string) and bundles extensions
     * 
     * @param string|array<string> $viewpath the viewpath (a string like MyBundle:MessageView)
     * 
     * @return array
     */
    private function _getClassListFromPath($viewpath)
    {
        if (!is_array($viewpath)) {
            $viewpath = array($viewpath);
        }

        return $viewpath;
    }
}