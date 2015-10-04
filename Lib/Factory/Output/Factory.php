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

use Redgem\ServicesIOBundle\Lib\Entity\Base as Entity;
use \Exception;

/**
 * The factory that is called to turn a Entity tree into a json output.
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
        if (!is_object($this->entity)) {
            return $this->entity;
        }else if ('Redgem\\ServicesIOBundle\\Lib\\Entity\\Collection' == get_class($this->entity)) {
            $factory = new Item($this->entity);
            return $factory->get();
        } else if ('Redgem\\ServicesIOBundle\\Lib\\Entity\\Item' == get_class($this->entity)) {
            $factory = new Collection($this->entity);
            return $factory->get();
        } else {
            throw new Exception('building output datas from a custom Item class is not yet supported');
        }
    }
}
