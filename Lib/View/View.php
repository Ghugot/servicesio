<?php
/**
 * View : view base class
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
use Redgem\ServicesIOBundle\Lib\Entity\Item;
use Redgem\ServicesIOBundle\Lib\Entity\Collection;
use Redgem\ServicesIOBundle\Lib\Entity\Base as Entity;

/**
 * the View class is the abstract basics that view classes should extend.
 *
 * @category View
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
abstract class View
{
    /**
     * the parameters sended to the view
     * 
     * @var array
     */
    private $_container;

    /**
     * the parameters sended to the view
     *
     * @var array
     */
    protected $params;

    /**
     * constructor
     * 
     * @param Container $container 
     */
    public function __construct(Container $container)
    {
        $this->_container = $container;        
    }

    /**
     * 
     * @param array $params
     * @return View
     */
    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * get a service
     * 
     * @param Container $service
     * 
     * @return mixed
     */
    protected function get($service)
    {
        return $this->_container->get($service);
    }

    /**
     * create a new collection for the view tree
     * 
     * @return Collection
     */
    protected function createCollection()
    {
        return $this->_setUpNewEntity(
            new Collection()
        );
    }

    /**
     * create a new item for the view tree
     *
     * @return Collection
     */
    protected function createItem()
    {
        return $this->_setUpNewEntity(
            new Item()
        );
    }

    /**
     *
     * @return string
     */
    public function getParent()
    {
        return null;
    }

    /**
     * 
     * @param array $params
     * 
     * @return Item
     */
    protected function content()
    {
        return $this->createCollection();
    }

    /**
     * call and execute a partial View class. The Entity is merged in the parent tree on the right place
     * all the params are forwarded to this new context, and merged with your additionnals.
     * 
     * @param string $path   the path for partial view class
     * @param string $params additional params
     */
    protected function partial($path, $params = array())
    {
        $render = new Render(
            $this->_container,
            $path,
            array_merge($this->params, $params)
        );
        
        return $render->get();
    }

    /**
     * set up the entity context
     * 
     * @param Entity $entity
     * @return Entity
     */
    private function _setUpNewEntity(Entity $entity)
    {
        $entity->setContainer($this->_container);
    
        return $entity;
    }
}