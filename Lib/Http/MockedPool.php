<?php
/**
 * Http : MockedPool
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
 * To handle and resolve the mocked requests.
 * This class is instanciated when mock is activated in configuration 
 *
 * @category Http
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class MockedPool extends Pool
{
    /**
     * @var array
     */
    private array $_responsesConfiguration;

    /**
     * @var string
     */
    private $_directory;

    /**
     * the constructor
     */
    public function __construct(LoggerInterface $monolog = null, array $responsesConfiguration, string $directory)
    {
        $this->_responsesConfiguration = $responsesConfiguration;
        $this->_directory = $directory;
        parent::__construct($monolog);
    }

    /**
     * query the requests, build and push the results
     *
     * @return Pool
     */
    public function send()
    {
        foreach($this->_requests as $request) {
            $request->setResponse(
                $this->_mock($request)
            );
        }

        return parent::send();
    }

    /**
     * @param Request $request
     * @return Response
     */
    private function _mock(Request $request) : ?Response
    {
        foreach($this->_responsesConfiguration as $responseCandidate) {

            if (preg_match(
                sprintf(
                    '|^%s$|Ui',
                    str_replace('*', '(.*)', $responseCandidate['url'])
                ),
                $request->getUrl()
            )) {
                if (!isset($responseCandidate['method'])
                    || $responseCandidate['method'] == $request->getMethod()
                ) {
                    return $this->_mockMatch($responseCandidate['file']);
                }
            }
        }

        return null;
    }

    /**
     * @param Request $request
     * @return Response
     */
    private function _mockMatch(string $file) : ?Response
    {
        if (!file_exists($this->_directory . $file)) {
            return null;
        }

        return new Response(
            file_get_contents($this->_directory . $file),
            203,
            []
        );
    }
}
