<?php
/**
 * Http : Pool
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
class Pool
{
    /**
     *
     * @var array<Request>
     */
    private $_requests;

    /**
     * the constructor
     */
    public function __construct()
    {

    }

    /**
     * Add a request in the pool
     *
     * @param Request $request the request
     *
     * @return Pool
     */
    public function addRequest(Request &$request)
    {
        $this->_requests[] = $request;
    
        return $this;
    }

    /**
     * count how many requests do we have
     * 
     * @return int
     */
    public function nbRequests()
    {
        return count($this->_requests);
    }

    /**
     * query the requests, build and push the results
     *
     * @return Pool
     */
    public function send()
    {
        $mh = curl_multi_init();

        foreach($this->_requests as $request) {
            curl_multi_add_handle($mh, $request->getCurlRequest());
        }

        $running = null;
        do {
            curl_multi_exec($mh,$running);
        } while ($running > 0);

        foreach($this->_requests as $request) {
            $rawResponse = curl_multi_getcontent($request->getCurlRequest());

            $headerSize = curl_getinfo(
                $request->getCurlRequest(),
                CURLINFO_HEADER_SIZE
            );

            $headers = array();
            foreach(explode("\r\n", substr($rawResponse, 0, $headerSize)) as $header) {
                $pos = strpos($header, ':');
                if(false !== $pos) {
                    $name  = trim(substr($header, 0, $pos));
                    $value = trim(substr($header, $pos + 1));
                    $headers[$name] = $value;
                }
            }
            
            $response = new Response(
                substr($rawResponse, $headerSize),
                curl_getinfo(
                    $request->getCurlRequest(),
                    CURLINFO_HTTP_CODE
                ),
                $headers
            );
            
            $request->setResponse($response);
        }

        foreach($this->_requests as $request) {
            curl_multi_remove_handle($mh, $request->getCurlRequest());
            curl_close($request->getCurlRequest());
        }

        curl_multi_close($mh);

        return $this;
    }
}
