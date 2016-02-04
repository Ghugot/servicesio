<?php
/**
 * Http : service accessor
 *
 * PHP Version 5
 *
 * @category Http
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */

namespace Redgem\ServicesIOBundle\Lib\Http;

/**
 * the Service class furnish helpers to build
 * the differents objects needed by ServicesIO Http requesting
 *
 * @category Http
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Service
{    
    /**
     * the constructor
     */
    public function __construct()
    {

    }

    /**
     * Create a new Request
     *
     * @return Request
     */
    public function createRequest()
    {
        return new Request();
    }

    /**
     * Create a new Pool
     *
     * @return Pool
     */
    public function createPool()
    {
        return new Pool();
    }
}
