<?php
/**
 * Entities : base
 *
 * PHP Version 5
 *
 * @category Entities
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */

namespace Redgem\ServicesIOBundle\Lib\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * the Base entity is the root class for any entities
 *
 * @category Entities
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Base
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
     * Is this entity empty ?
     * 
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->datas);
    }

    /**
     * count the number of elements of the entity
     * 
     * @return int
     */
    public function count()
    {
        return count($this->datas);
    }
}
