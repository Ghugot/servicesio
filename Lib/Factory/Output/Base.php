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

namespace Redgem\ServicesIOBundle\Lib\Factory\Output;

/**
 * The factory that is called to turn a nodes tree into a json output.
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
     * the Node to extract
     * 
     * @var Node
     */
    protected $node;
    
    /**
     * the constructor
     *
     * @param Node  $node the root node
     */
    public function __construct($node)
    {
        $this->node = $node;
    }
}
