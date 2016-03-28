<?php
/**
 * Nodes : item
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

use Redgem\ServicesIOBundle\Lib\Node\Exception\Undefined;

/**
 * the Item node handle structured datamodels
 *
 * @category Nodes
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Item extends Collection
{
    /**
     * if this field is actually a placeholder, its name
     *
     * @var string
     */
    protected $placecholders;

    /**
     * regular getter for a field of the node
     * 
     * @param string $key     the field name
     * @param mixed  $default the default value if the field doesn't exists
     * 
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (!array_key_exists($key, $this->datas)) {
            return $default;
        }

        return $this->datas[$key];
    }

    /**
     * regular setter for a field of the node
     * 
     * @param string $key         the field name
     * @param mixed  $value       the value
     * @param string $placeholder if this field is actually a placeholder, its name
     * 
     * @return Item
     */
    public function set($key, $value, $placeholder = null)
    {
        $this->datas[$key] = $value;
        $this->placecholders[$key] = $placeholder;

        return $this;
    }

    /**
     * if this field is actually a placeholder, its name
     *
     * @param string $key     the field name
     *
     * @return string
     */
    public function getPlaceholder($key)
    {
        if (!isset($this->placecholders[$key])) {
            return null;
        }
    
        return $this->placecholders[$key];
    }

    /**
     * the magic caller for getXXX()
     * 
     * @param unknown $name      the called function name
     * @param unknown $arguments the called function arguments
     * 
     * @throws Undefined
     * 
     * @return mixed
     */
    public function __call($name, $arguments)
    {
    	if ('all' == $name) {
    		return null;
    	}

    	if ('has' == substr($name, 0, 3)) {
    		$key = substr($name, 3, strlen($name));

    		if (array_key_exists($key, $this->datas)) {
    			return true;
    		}
    		if (array_key_exists(lcfirst($key), $this->datas)) {
    			return true;
    		}
    		return false;
    	}

        if ('get' == substr($name, 0, 3)) {
            $key = substr($name, 3, strlen($name));

            if (array_key_exists($key, $this->datas)) {
            	return $this->datas[$key];
            } 
            if (array_key_exists(lcfirst($key), $this->datas)) {
            	return $this->datas[lcfirst($key)];
            }
        }

        if (array_key_exists($name, $this->datas)) {
        	return $this->datas[$name];
        }

        throw new Undefined();
    }
}
