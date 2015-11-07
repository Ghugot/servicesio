<?php
/**
 * Factories : base
 *
 * PHP Version 5
 *
 * @category Factories
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */

namespace Redgem\ServicesIOBundle\Lib\Factory\Input;

use Redgem\ServicesIOBundle\Lib\Node\Node;

/**
 * The factory that is called to turn a json into a node tree.
 * It has a recursive usage.
 *
 * @category Factories
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Base
{
    /**
     * the embeded nodes for subtree
     *
     * @var array
     */
    protected $nodes;
    
    /**
     * the whole config to pass to the tree
     *
     * @var array
     */
    protected $config;

    /**
     * the node's extension we want to use
     *
     * @var string
     */
    protected $customNodeClass;
    
    /**
     * constructor
     *
     * @param array  $nodes           the raw datas
     * @param array  $config          the Model extensions configuration
     * @param string $customNodeClass the extended entity we want to use
     */
    public function __construct(array $nodes, $config = null, $customNodeClass = null)
    {
        $this->nodes = $nodes;
        $this->customNodeClass = $customNodeClass;
        $this->config = $config;
    }
    
    /**
     * Get the node to build
     *
     * @return Base
     */
    public function get()
    {
        return new Node();
    }
    
    /**
     * Create the new node accorded to provided config
     *
     * @param string $class   the configurated class
     * @param Node   $default the default node object if no class is configured
     *
     * @return Item
     */
    protected function createNode($class, Node $default)
    {
        if (!$class) {
            return $default;
        }
    
        if (!class_exists($class)) {
            throw new RuntimeException(
                sprintf(
                    'class "%s" doesn\'t exists. Did you made a typo ?',
                    $class
                )
            );
        }
    
        $reflexionObject = new ReflectionClass($class);
        $o = $reflexionObject->newInstance();

        if (!is_subclass_of(
            $o,
            'Redgem\\ServicesIOBundle\\Lib\\Node\\Node'
        )
        ) {
            throw new RuntimeException(
                sprintf(
                    'class "%s" doesn\'t extends Node.
                    Extensions class must extends
                    "Redgem\ServicesIOBundle\Lib\Node\Node"',
                    $class
                )
            );
        }

        $o->setContainer(
            Builder::getContainer()
        );
    
        return $o;
    }

    /**
     * Split configs paths for recursive parsing :
     * - remove parent part if we are on the right branch
     * - remove other branchs configuration
     *
     * @param array  $config the original config array
     * @param string $path   the current path to split on
     *
     * @return array
     */
    protected function getConfigForSubpath($config = null, $path = null)
    {
        if (!$config) {
            return null;
        }

        if (is_numeric($path)) {
            foreach ($config as $key => $item) {
                if ('[]' == substr($item['path'], 0, 2)) {
                    $config[$key]['path'] = substr($item['path'], 2);
                } else {
                    unset($config[$key]);
                }
            }
        } else {
            foreach ($config as $key => $item) {
                if ('/'.$path == substr($item['path'], 0, strlen($path) + 1)) {
                    $config[$key]['path'] = substr($item['path'], strlen($path) + 1);
                } else {
                    unset($config[$key]);
                }
            }
        }
    
        return (sizeof($config) > 0) ? $config : null;
    }
}
