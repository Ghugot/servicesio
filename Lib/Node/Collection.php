<?php
/**
 * Entities : collection
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

use \ArrayAccess;
use \Countable;
use \IteratorAggregate;
use \ArrayIterator;

/**
 * the Collection node handle pure array of datamodels
 *
 * @category Nodes
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Collection extends Node implements ArrayAccess, Countable, IteratorAggregate
{    
    /**
     * ArrayAccess interface method implmentation
     *
     * @param mixed $value the value to set
     *
     * @return Collection
     */
    public function push($value)
    {
        $this->datas[] = $value;
    
        return $this;
    }

    /**
     * Get the first element of the node
     *
     * @return mixed
     */
    public function getFirst()
    {
        return reset($this->datas);
    }
    
    /**
     * Get the last element of the node
     *
     * @return mixed
     */
    public function getLast()
    {
        return end($this->datas);
    }

    /**
     * IteratorAggregate interface method implmentation
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->datas);
    }

    /**
     * ArrayAccess interface method implmentation
     * 
     * @param mixed $offset the array offset
     * 
     * @return null
     */
    public function offsetGet($offset)
    {
        if (!isset($this->datas[$offset])) {
            return null;
            
        }

        return $this->datas[$offset];
    }

    /**
     * ArrayAccess interface method implmentation
     * 
     * @param mixed $offset the array offset
     * 
     * @return boolean
     */
    public function offsetExists($offset)
    {
        throw new \InvalidArgumentException(
            'This method is not yet implemented'
        );

        return $this;
    }

    /**
     * ArrayAccess interface method implmentation
     * 
     * @param mixed $offset the array offset
     * @param mixed $value  the value
     * 
     * @return Collection
     */
    public function offsetSet($offset, $value)
    {
        throw new \InvalidArgumentException(
            'This method is not yet implemented'
        );

        return $this;
    }

    /**
     * ArrayAccess interface method implmentation
     * 
     * @param mixed $offset the array offset
     * 
     * @throws InvalidArgumentException
     * 
     * @return null
     */
    public function offsetUnset($offset)
    {
        throw new \InvalidArgumentException(
            'This method is not yet implemented'
        );
    }
}
