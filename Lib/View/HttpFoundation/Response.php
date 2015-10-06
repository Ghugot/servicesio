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
use Redgem\ServicesIOBundle\Lib\Entity\Base as Entity;

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
     * the source Entity of the response
     * 
     * @var Entity
     */
    private $_source;

    /**
     * get the source Entity
     *
     * @return Entity
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * set the source Entity
     * 
     * @param Entity $source
     * @return Response
     */
    public function setSource($source)
    {
        $this->_source = $source;
        
        return $this;
    }
}