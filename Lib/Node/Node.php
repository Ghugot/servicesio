<?php
/**
 * Nodes : base
 *
 * PHP Version 5
 *
 * @category Nodes
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */

namespace Redgem\ServicesIOBundle\Lib\Node;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Redgem\ServicesIOBundle\Lib\Node\Exception\Undefined;

/**
 * the Node class is the root class for any nodes
 *
 * @category Nodes
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Node
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Array
     */
    protected $datas = array();

    /**
     * setter for the container
     * 
     * @param Container $container The Symfony container
     * 
     * @return Base
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Is this node empty ?
     * 
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->datas);
    }

    /**
     * count the number of elements of the node
     * 
     * @return int
     */
    public function count()
    {
        return count($this->datas);
    }

    /**
     * Never break a get()
     *
     * @param unknown $name      the called function name
     * @param unknown $arguments the called function arguments
     *
     * @throws Undefined
     */
    public function __call($name, $arguments)
    {    
        throw new Undefined();
    }
}
