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

class GetPrefixCest
{
    use DatabaseTrait;

    /**
     * Tests Phalcon\Incubator\Cache\Database :: getPrefix()
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2019-03-31
     */
    public function cacheAdapterDatabaseGetSetPrefix(FunctionalTester $I)
    {
        $I->wantToTest('Cache\Adapter\Database - getPrefix()');

        $serializer = new SerializerFactory();
        $adapter    = new Database(
            $serializer,
            array_merge(
                $this->getOptions(),
                [
                    'prefix' => 'my-prefix',
                ]
            )
        );

        $expected = 'my-prefix';
        $actual   = $adapter->getPrefix();
        $I->assertEquals($expected, $actual);
    }

    /**
     * Tests Phalcon\Incubator\Cache\Database :: getPrefix() - default
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2019-03-31
     */
    public function cacheAdapterDatabaseGetSetPrefixDefault(FunctionalTester $I)
    {
        $I->wantToTest('Cache\Adapter\Database - getPrefix() - default');

        $serializer = new SerializerFactory();
        $adapter    = new Database($serializer, $this->getOptions());

        $expected = '';
        $actual   = $adapter->getPrefix();
        $I->assertEquals($expected, $actual);
    }
}
