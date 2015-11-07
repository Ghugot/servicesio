<?php
/**
 * Tests
 *
 * PHP Version 5
 *
 * @category Tests
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */

namespace Redgem\ServicesIOBundle\Tests\Lib\View\HttpFoundation;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Redgem\ServicesIOBundle\Lib\View\HttpFoundation\Response;

/**
 * Tests
 *
 * @category Tests
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class ResponseTest extends WebTestCase
{    
    /**
     * @test
     */
    public function getSourceTest()
    {
        $node = $this->getMockBuilder(
                'Redgem\\ServicesIOBundle\\Lib\\Node\\Node'
            )
            ->disableOriginalConstructor()
            ->getMock();

        $response = new Response();
        $response->setSource($node);

        $this->assertTrue(
            is_a($response->getSource(), 'Redgem\ServicesIOBundle\Lib\Node\Node')
        );
    }
}