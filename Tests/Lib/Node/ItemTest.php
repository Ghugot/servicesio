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
use Redgem\ServicesIOBundle\Lib\Node\Item;

/**
 * Tests
 *
 * @category Tests
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class ItemTest extends WebTestCase
{    
    /**
     * @test
     */
    public function getTest()
    {
        $item = new Item();
        $item
            ->set('test1', 1)
            ->set('test2', 2)
            ->set('test3', 3);

        $this->assertThat($item->get('test2'), $this->equalTo(2));
    }

    /**
     * @test
     */
    public function getPlaceholderTest()
    {
        $item = new Item();
        $item
            ->set('test1', 1)
            ->set('test2', 2, 'placeholderName')
            ->set('test3', 3);
    
        $this->assertNull($item->getPlaceholder('test1'));
        $this->assertThat($item->getPlaceholder('test2'), $this->equalTo('placeholderName'));
    }

    /**
     * @test
     */
    public function callTest()
    {
        $item = new Item();
        $item
            ->set('test1', 1)
            ->set('test2', 2)
            ->set('test3', 3);
    
        $this->assertThat($item->getTest2(), $this->equalTo(2));
        $this->assertNull($item->getTest4());
    }
}