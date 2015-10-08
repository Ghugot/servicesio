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

namespace Redgem\ServicesIOBundle\Tests\Lib\Node;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Redgem\ServicesIOBundle\Lib\Node\Node;

/**
 * Tests
 *
 * @category Tests
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class NodeTest extends WebTestCase
{
    /**
     * @test
     */
    public function isEmptyTest()
    {
        $node = new Node();
        $this->assertTrue($node->isEmpty());
        $this->assertThat($node->count(), $this->equalTo(0));
    }

    /**
     * @test
     */
    public function countTest()
    {
        $node = new Node();
        $this->assertThat($node->count(), $this->equalTo(0));
    }
}