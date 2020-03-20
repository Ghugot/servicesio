<?php
/**
 * Http : Request
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

use \Exception;

/**
 * The request representation for a Http query.
 *
 * @category Http
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Request
{
    /**
     * @var string
     */
    private $_url;

    /**
     * @var string
     */
    private $_body;

    /**
     * @var array<string>
     */
    private $_parameters;

    /**
     * @var array<string>
     */
    private $_headers;

    /**
     * @var string
     */
    private $_method;

    /**
     * @var string
     */
    private $_cookiesJar;

    /**
     * @var string
     */
    private $_referer;

    /**
     * @var string
     */
    private $_userAgent;

    /**
     * @var string
     */
    private $_interface;

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    const CONTENTTYPE_JSON = 'application/json';

    /**
     *
     * @var Response
     */
    protected $_response;

    /**
     *
     * @var Resource
     */
    protected $_curlRequest;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->_parameters = array();
        $this->_headers = array();
    }

    /**
     * Get the request URL
     *
     * @param bool $build do we want to build the final URL with parameters ?
     *
     * @return string
     */
    public function getUrl($build = false)
    {
        if (!$build) {
            return $this->_url;
        }

        $a = parse_url($this->_url);

        $url = '';

        if (isset($a['scheme'])) {
            $url = $a['scheme'] . '://';
        } else {
            $url = 'http://';
        }

        if (!isset($a['host'])) {
            throw new Exception('URL must be absolute');
        }

        $url = $url . $a['host'];
	
        if (isset($a['port'])) {
            $url = $url . ':' . $a['port'];
        }

        if (isset($a['path'])) {
            $url = $url . $a['path'];
        }

        if (isset($a['query'])) {
            $url = $url . '?' . $a['query'];
        }

        if (sizeof($this->_parameters) == 0) {
            return $url;
        }

        $a = [];
        foreach ($this->_parameters as $key => $value) {
            $a[] = sprintf('%s=%s', $key, $value);
        }

        return sprintf('%s%s%s',
            $url,
            ((strpos($this->_url, '?') === false) ? '?' : '&'),
            implode('&', $a)
        );
    }

    /**
     * Set the request URL
     *
     * @param string $url the request URL
     *
     * @return Request
     */
    public function setUrl($url)
    {
        $this->onUpdate();

        $this->_url = $url;

        return $this;
    }

    /**
     * Get headers of this request
     *
     * @return array<string>
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * get a header by name
     *
     * @param string $name
     *
     * @return string
     */
    public function getHeader($name)
    {
        if (!isset($this->_headers[$name])) {
            return null;
        }
    
        return $this->_headers[$name];
    }

    /**
     * Add header to this request
     *
     * @param string $key   the header name
     * @param string $value the header value
     *
     * @return Request
     */
    public function addHeader($key, $value)
    {
        $this->onUpdate();

        $this->_headers[$key] = $value;

        return $this;
    }

    /**
     * Get parameters of this request
     *
     * @return array<string>
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * add a GET query parameter
     *
     * @param string $key   the parameter name
     * @param string $value the parameter value
     *
     * @return Request
     */
    public function addParameter($key, $value)
    {
        $this->onUpdate();

        $this->_parameters[$key] = $value;

        return $this;
    }

    /**
     * Get body of this request
     *
     * @return array<string>
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * Set body to this request
     *
     * @param string $body the body plain value
     *
     * @return Request
     */
    public function setBody($body)
    {
        $this->onUpdate();
    
        $this->_body = $body;
    
        return $this;
    }

    /**
     * Get the method of this request
     *
     * @return string
     */
    public function getMethod()
    {
        if ($this->_method) {
            return $this->_method;
        }
        
        return $this->getBody() ? self::METHOD_POST : self::METHOD_GET;
    }
    
    /**
     * Set the method to this request
     *
     * @param string $method the method value
     *
     * @return Request
     */
    public function setMethod($method)
    {
        $this->onUpdate();

        if (!in_array(
                $method,
                array(self::METHOD_GET, self::METHOD_DELETE, self::METHOD_POST, self::METHOD_PUT,)
            )
        ) {
            throw new Exception(
                sprintf('method "%s" is not accepted.', $method)
            );
        }

        $this->_method = $method;

        return $this;
    }

    /**
     * Get the cookies jar
     *
     * @return String
     */
    public function getCookiesJar()
    {
        return $this->_cookiesJar;
    }

    /**
     * Set the cookies jar
     *
     * $cookiesJar string the cookie jar path
     *
     * @return Request
     */
    public function setCookiesJar($cookiesJar)
    {
        $this->_cookiesJar = $cookiesJar;

        return $this;
    }

    /**
     * Get the referer
     *
     * @return String
     */
    public function getReferer()
    {
        return $this->_referer;
    }
    
    /**
     * Set the referer
     *
     * $cookiesJar string the referer url
     *
     * @return Request
     */
    public function setReferer($referer)
    {
        $this->_referer = $referer;

        return $this;
    }

    /**
     * Get the user agent
     *
     * @return String
     */
    public function getUserAgent()
    {
        return $this->_userAgent;
    }

    /**
     * Set the user agent
     *
     * $cookiesJar string the user agent name
     *
     * @return Request
     */
    public function setUserAgent($userAgent)
    {
        $this->_userAgent = $userAgent;

        return $this;
    }

    /**
     * Get the network interface to use.
     *
     * @return string
     */
    public function getInterface()
    {
    	return $this->_interface;   
    }

    /**
     * Set the network interface to use.
     *
     * @param string $interfaces the interface.
     *
     * @return Request
     */
    public function setInterface($interface)
    {
    	$this->_interface = $interface;

   		return $this;
    }

    /**
     * build the corresponding Curl Request
     *
     * @return Resource
     */
    public function getCurlRequest()
    {
        if ($this->_curlRequest) {
            return $this->_curlRequest;
        }
    
        $headers = array();
        foreach($this->getHeaders() as $key => $name) {
            $headers[] = sprintf('%s: %s', $key, $name);
        }

        if ($this->getUserAgent()) {
            $headers[] = sprintf('User-Agent: %s', $this->getUserAgent());
        }

        $this->_curlRequest = curl_init();

        curl_setopt($this->_curlRequest, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->_curlRequest, CURLOPT_URL, $this->getUrl(true));
        curl_setopt($this->_curlRequest, CURLOPT_CUSTOMREQUEST, $this->getMethod());
        curl_setopt($this->_curlRequest, CURLOPT_HEADER, 1);
        curl_setopt($this->_curlRequest, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->_curlRequest, CURLOPT_FRESH_CONNECT, false);
        curl_setopt($this->_curlRequest, CURLOPT_FORBID_REUSE, false);
        curl_setopt($this->_curlRequest, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->_curlRequest, CURLOPT_MAXREDIRS, 3);

        if ($this->getCookiesJar()) {
            curl_setopt($this->_curlRequest, CURLOPT_COOKIEJAR, $this->getCookiesJar());
            curl_setopt($this->_curlRequest, CURLOPT_COOKIEFILE, $this->getCookiesJar());
        }
        
        if ($this->getReferer()) {
            curl_setopt($this->_curlRequest, CURLOPT_REFERER, $this->getReferer());
        }

        if ($this->getInterface()) {
        	curl_setopt($this->_curlRequest, CURLOPT_INTERFACE, $this->getInterface());
        }

        if ($this->getBody()) {
            curl_setopt($this->_curlRequest, CURLOPT_POST, 1);
            curl_setopt($this->_curlRequest, CURLOPT_POSTFIELDS, $this->getBody());
        }

        return $this->_curlRequest;
    }

    /**
     * get the response after querying
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * set the response for this query. You shouldn't call that.
     *
     * @param Response $response the response from server
     *
     * @throws Exception
     *
     * @return Request
     */
    public function setResponse(Response $response)
    {
        if (!$this->_curlRequest) {
            throw new Exception(
                'This request has not been sent yet'
            );
        }

        $this->_response = $response;

        return $this;
    }
    
    /**
     * check if this query can be updated
     *
     * @throws Exception
     * @return null
     */
    private function onUpdate()
    {
        if ($this->_curlRequest) {
            throw new Exception(
                sprintf(
                    'Can\'t update "%s" request because it has already been sent.',
                    $this->getUrl()
                )
            );
        }
    }   
}