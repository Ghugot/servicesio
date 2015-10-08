<?php
/**
 * Factories : factory base
 *
 * PHP Version 5
 *
 * @category Factories
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */

namespace Redgem\ServicesIOBundle\Lib\Factory\Output;

use \Exception;

/**
 * The factory that is called to turn a nodes tree into a json output.
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
     * get the extracted datas
     * 
     * @throws Exception
     * 
     * @return array
     */
    public function get()
    {
        if (!is_object($this->node)) {
            return $this->node;
        } else if ('Redgem\\ServicesIOBundle\\Lib\\Node\\Collection' == get_class($this->node)) {
            $factory = new Collection($this->node);
            return $factory->get();
        } else if ('Redgem\\ServicesIOBundle\\Lib\\Node\\Item' == get_class($this->node)) {
            $factory = new Item($this->node);
            return $factory->get();
        } else {
            throw new Exception('building output datas from a custom Item class is not yet supported');
        }
    }
}
