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

use Psr\Log\LoggerInterface;

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
	 * @var LoggerInterface
	 */
	private $_monolog;

    /**
     *
     * @var array<Request>
     */
    private $_requests;

    /**
     *
     * @var float
     */
    private $_time;

    /**
     * the constructor
     */
    public function __construct(LoggerInterface $monolog = null)
    {
		$this->_monolog = $monolog;
		$this->_requests = array();
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
     * get the requests
     * 
     * @return array<Request>
     */
    public function getRequests()
    {
        return $this->_requests;
    }

    /**
     * get the number of embedded requests
     *
     * @return int
     */
    public function nbRequests()
    {
    	return count($this->_requests);
    }

    /**
     * get querying time
     *
     * @return float
     */
    public function getTime()
    {
    	return $this->_time;
    }

    /**
     * query the requests, build and push the results
     *
     * @return Pool
     */
    public function send()
    {
    	$start = microtime(true);

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

            $code = curl_getinfo(
                $request->getCurlRequest(),
                CURLINFO_HTTP_CODE
            );

            $response = new Response(
                ($code == 0) ? '' : substr($rawResponse, $headerSize),
                $code,
                $headers
            );

            $request->setResponse($response);
            $this->_log($request);
        }

        foreach($this->_requests as $request) {
            curl_multi_remove_handle($mh, $request->getCurlRequest());
            curl_close($request->getCurlRequest());
        }

        curl_multi_close($mh);

        $this->_time = round((microtime(true) - $start) * 1000);

        return $this;
    }

    /**
     * push in Monolog the request
     * 
     * @param Request $request
     */
    private function _log(Request $request)
    {
    	$params = array(
    		'url' => $request->getUrl(true),
    	);

    	if ($request->getReferer()) {
    		$params['referer'] = $request->getReferer();
    	}

    	if ($request->getUserAgent()) {
    		$params['user-agent'] = $request->getUserAgent();
    	}

    	if ($request->getCookiesJar()) {
    		$params['cookies-jar'] = $request->getCookiesJar();
    	}

    	if ($request->getInterface()) {
    		$params['interface'] = $request->getInterface();
    	}

    	$params['status'] = $request->getResponse()->getStatusCode();

    	$this->_monolog->info(
    		'servicesio_http request',
    		$params
    	);
    }
}
