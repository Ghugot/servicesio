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

use Redgem\ServicesIOBundle\Lib\Entity\Base as Entity;

/**
 * The factory that is called to turn a Entity tree into a json output.
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
     * the Entity to extract
     * 
     * @var Entity
     */
    protected $entity;
    
    /**
     * the constructor
     *
     * @param array  $datas        the raw datas
     * @param string $customEntity the extended entity we want to use
     * @param array  $config       the Wrapper extensions configuration
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }
}
