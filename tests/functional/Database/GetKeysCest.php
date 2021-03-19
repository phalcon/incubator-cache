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

class GetKeysCest
{
    use DatabaseTrait;

    /**
     * Tests Phalcon\Incubator\Cache\Database :: getKeys()
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2019-04-13
     */
    public function cacheAdapterDatabaseGetKeys(FunctionalTester $I)
    {
        $I->wantToTest('Cache\Adapter\Database - getKeys()');

        $serializer = new SerializerFactory();
        $adapter    = new Database($serializer, $this->getOptions());

        $actual = $adapter->clear();
        $I->assertTrue($actual);

        $key = 'key-1';
        $adapter->set($key, 'test');
        $actual = $adapter->has($key);
        $I->assertTrue($actual);

        $key = 'key-2';
        $adapter->set($key, 'test');
        $actual = $adapter->has($key);
        $I->assertTrue($actual);

        $expected = [
            'key-1',
            'key-2',
        ];
        $actual   = $adapter->getKeys();
        sort($actual);
        $I->assertEquals($expected, $actual);
    }
}
