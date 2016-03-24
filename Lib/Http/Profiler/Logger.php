<?php
/**
 * Http : Profiler
 *
 * PHP Version 5
 *
 * @category Http
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */

namespace Redgem\ServicesIOBundle\Lib\Http\Profiler;

use Redgem\ServicesIOBundle\Lib\Http\Pool;

/**
 * Log all the pools created for the profiler tool
 *
 * @category Http
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Logger
{
    /**
     * @var array
     */
    private $_pools = array();

    /**
     * Link a pool to logging.
     *
     * @param Pool $pool the pool to log
     *
     * @return Logger
     */
    public function addPool(Pool $pool)
    {
        $this->_pools[] = $pool;

        return $this;
    }

    /**
     * get the data array for dataCollector
     *
     * @return array
     */
    public function getDatas()
    {
        $a = array();
        foreach ($this->_pools as $pool) {
            $r = array();
            foreach ($pool->getRequests() as $request) {
                $r[] = array(
                    'url' => $request->getUrl(true),
                    'success' => $request->getResponse() ? $request->getResponse()->isSuccess() : false,
                    'status' => $request->getResponse() ? $request->getResponse()->getStatusCode() : -1,
                );
            }

            $a[] = array(
                'requests' => $r,
                'time' => $pool->getTime(),
            );
        }

        return $a;
    }
}