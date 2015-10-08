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
        if (!isset($this->datas[strtolower($key)])) {
            return $default;
        }

        return $this->datas[strtolower($key)];
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
        $this->datas[strtolower($key)] = $value;
        $this->placecholders[strtolower($key)] = $placeholder;

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
        if (!isset($this->placecholders[strtolower($key)])) {
            return null;
        }
    
        return $this->placecholders[strtolower($key)];
    }

    /**
     * the magic caller for getXXX()
     * 
     * @param unknown $name      the called function name
     * @param unknown $arguments the called function arguments
     * 
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if ('get' == substr($name, 0, 3)) {
            $key = substr($name, 3, strlen($name));
            return $this->get($key);
        }

        return null;
    }
}
