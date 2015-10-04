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
class Collection extends Factory
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
        $v = array();
        foreach($this->entity as $key => $field) {
            $factory = new Factory($field);
            $v[$key] = $factory->get();
        }

        return $v;
    }
}
