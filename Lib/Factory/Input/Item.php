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

use Redgem\ServicesIOBundle\Lib\Node\Item as ItemNode;

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
class Item extends Base
{
    /**
     * Get the actual Node for an item according
     * to the provided configuration of available extensions
     *
     * @return Item
     */
    public function get()
    {
        $node = $this->createNode(
            $this->customNodeClass,
            new ItemNode()
        );
    
        foreach ($this->nodes as $key => $val) {
            $customNodeClass = null;

            if ($this->config) {
                foreach ($this->config as $item) {
                    if ('/'.$key == $item['path']) {
                        $customNodeClass = $item['class'];
                    }
                }
            }
    
            if (is_array($val)) {
                $factory = new Factory(
                    $val,
                    $this->getConfigForSubpath($this->config, $key),
                    $customNodeClass
                );
                $val = $factory->get();
            }
    
            $node->set($key, $val);
        }
    
        return $node;
    }
}
