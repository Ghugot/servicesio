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
class Factory extends Base
{
    /**
     * Get the instancied and hydrated object
     *
     * @return Base
     */
    public function get()
    {
        if (sizeof($this->nodes) == 0) {
            return new Base();
        }

        $assoc = false;
        foreach (array_keys($this->nodes) as $key) {
            if (!is_numeric($key) && !$assoc) {
                $assoc = true;
            }
        }

        if ($assoc) {
            $factory = new Item(
                $this->nodes,
                $this->customNodeClass,
                $this->config
            );

            return $factory->get();
        }

        $factory = new Collection(
            $this->nodes,
            $this->customNodeClass,
            $this->config
        );

        return $factory->get();
    }
}
