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
use Redgem\ServicesIOBundle\Lib\Node\Collection;

/**
 * Tests
 *
 * @category Tests
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class CollectionTest extends WebTestCase
{
    /**
     * @test
     */
    public function getIteratorTest()
    {
        $collection = new Collection();
        $collection
            ->push(1)
            ->push(2)
            ->push(3);
        $this->assertThat($collection[1], $this->equalTo(2));
    }

    /**
     * @test
     */
    public function getFirstTest()
    {
        $collection = new Collection();
        $collection
            ->push(1)
            ->push(2)
            ->push(3);
        $this->assertThat($collection->getFirst(), $this->equalTo(1));
    }

    /**
     * @test
     */
    public function getLastTest()
    {
        $collection = new Collection();
        $collection
            ->push(1)
            ->push(2)
            ->push(3);
        $this->assertThat($collection->getLast(), $this->equalTo(3));
    }
}
