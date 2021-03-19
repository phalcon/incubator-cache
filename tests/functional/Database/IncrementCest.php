<?php

/**
 * This file is part of the Phalcon Framework.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Incubator\Cache\Tests\Functional\Database;

use Phalcon\Incubator\Cache\Database;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Incubator\Cache\Test\Fixtures\Traits\Cache\DatabaseTrait;
use FunctionalTester;

use function getOptionsSqlite;

class IncrementCest
{
    use DatabaseTrait;

    /**
     * Tests Phalcon\Incubator\Cache\Database :: increment()
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2019-03-31
     */
    public function cacheAdapterDatabaseIncrement(FunctionalTester $I)
    {
        $I->wantToTest('Cache\Adapter\Database - increment()');

        $serializer = new SerializerFactory();
        $adapter    = new Database($serializer, $this->getOptions());

        $key    = 'cache-data';
        $result = $adapter->set($key, 1);
        $I->assertTrue($result);

        $expected = 2;
        $actual   = $adapter->increment($key);
        $I->assertEquals($expected, $actual);

        $actual = $adapter->get($key);
        $I->assertEquals($expected, $actual);

        $expected = 10;
        $actual   = $adapter->increment($key, 8);
        $I->assertEquals($expected, $actual);

        $actual = $adapter->get($key);
        $I->assertEquals($expected, $actual);

        /**
         * unknown key
         */
        $key    = 'unknown';
        $result = $adapter->increment($key);
        $I->assertFalse($result);
    }
}
