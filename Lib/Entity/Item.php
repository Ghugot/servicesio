<?php
/**
 * Entities : item
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

/**
 * the Item entity handle structured datamodels
 *
 * @category Entities
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Item extends Collection
{
    /**
     * regular getter for a field of the entity
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
     * regular setter for a field of the entity
     * 
     * @param string $key   the field name
     * @param mixed  $value the value
     * 
     * @return Item
     */
    public function set($key, $value)
    {
        $this->datas[strtolower($key)] = $value;

        return $this;
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
