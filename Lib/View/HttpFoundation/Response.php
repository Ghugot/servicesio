<?php
/**
 * View : controller response
 *
 * PHP Version 5
 *
 * @category View
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */

namespace Redgem\ServicesIOBundle\Lib\View\HttpFoundation;

use Symfony\Component\HttpFoundation\Response as Base;

/**
 * The HTTP response
 *
 * @category View
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Response extends Base
{
    /**
     * the source node of the response
     * 
     * @var Node
     */
    private $_source;

    /**
     * get the source node
     *
     * @return Node
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * set the source node
     * 
     * @param Node $source
     * @return Response
     */
    public function setSource($source)
    {
        $this->_source = $source;
        
        return $this;
    }
}