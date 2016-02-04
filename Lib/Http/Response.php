<?php
/**
 * Http : Response
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

use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

/**
 * The response representation for a Http query.
 *
 * @category Http
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Response extends HttpFoundationResponse
{
    /**
     * get raw body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->getContent();
    }

    /**
     * set body for raw datas
     *
     * @param mixed $body the content
     *
     * @return Response
     */
    public function setBody($body)
    {
        $this->content = $content;
    
        return $this;
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
        return $this->headers->get($name);
    }

    /**
     * is this response a success ?
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return ($this->getStatusCode() >= 200 && $this->getStatusCode() < 300);
    }
}