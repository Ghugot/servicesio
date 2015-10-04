<?php
/**
 * View : tree parser for placeholder replacements
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

use Redgem\ServicesIOBundle\Lib\Entity\Base as Entity;

/**
 * the Parser class catch and replace all the found placeholders in the Entities tree
 * with their implementation in the View hierarchy.
 *
 * @category View
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Parser
{
    /**
     * The root entity for tree content
     * 
     * @var Entity
     */
    private $_entity;
    
    /**
     * the instancied view to find methods for replacement
     * 
     * @var View
     */
    private $_view;

    /**
     * constructor
     * 
     * @param Entity $entity
     * @param View $view
     */
    public function __construct(Entity $entity, View $view)
    {
        $this->_entity = $entity;
        $this->_view = $view;
    }

    /**
     * replace all the placeholders in the tree and return the new content;
     * 
     * @return Entity;
     */
    public function getReplacedContent()
    {
        if ('Redgem\\ServicesIOBundle\\Lib\\Entity\\Item' != get_class($this->_entity)) {
            return $this->_entity;
        }

        foreach($this->_entity as $key => $val) {
            if ($this->_entity->getPlaceholder($key)) {
                $this->_entity->set(
                    $key,
                    $this->findPlaceholderDatas($this->_entity->getPlaceholder($key)),
                    $this->_entity->getPlaceholder($key)
                );
            }
        }

        foreach($this->_entity as $key => $val) {
            if (is_object($val)) {
                $parser = new self($val, $this->_view);
                $this->_entity->set($key, $parser->getReplacedContent());
            }
        }

        return $this->_entity;
    }

    /**
     * find the placeholder datas from the view if avalable, and replace default values
     * 
     * @param string $name
     * 
     * @return Entity
     */
    private function findPlaceholderDatas($name)
    {
        $methodName = 'block' . ucFirst($name);
        if (!method_exists($this->_view, $methodName)) {
            return $name;
        }

        return $this->_view->$methodName();
    }
}