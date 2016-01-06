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
     * The root node for tree content
     * 
     * @var Node
     */
    private $_node;
    
    /**
     * the instancied view to find methods for replacement
     * 
     * @var View
     */
    private $_view;

    /**
     * constructor
     * 
     * @param Node $node
     * @param View $view
     */
    public function __construct($node, View $view)
    {
        $this->_node = $node;
        $this->_view = $view;
    }

    /**
     * replace all the placeholders in the tree and return the new content;
     * 
     * @return Node;
     */
    public function getReplacedContent()
    {
        if ('Redgem\\ServicesIOBundle\\Lib\\Node\\Item' != get_class($this->_node)) {
            return $this->_node;
        }

        foreach($this->_node as $key => $val) {
            if ($this->_node->getPlaceholder($key)) {
                $replacedValue = $this->findPlaceholderDatas($this->_node->getPlaceholder($key));
                if ($replacedValue) {
                    $this->_node->set(
                        $key,
                        $replacedValue,
                        $this->_node->getPlaceholder($key)
                    );
                }
            }
        }

        foreach($this->_node as $key => $val) {
            if (is_object($val)) {
                $parser = new self($val, $this->_view);
                $this->_node->set($key, $parser->getReplacedContent(), $this->_node->getPlaceholder($key));
            }
        }

        return $this->_node;
    }

    /**
     * find the placeholder datas from the view if avalable, and replace default values
     * 
     * @param string $name
     * 
     * @return Node
     */
    private function findPlaceholderDatas($name)
    {
        $methodName = 'block' . ucFirst($name);

        if (!method_exists($this->_view, $methodName)) {
            return null;
        }

        return $this->_view->$methodName();
    }
}