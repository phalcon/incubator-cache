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
use Phalcon\Incubator\Cache\Test\Fixtures\Traits\Cache\DatabaseTrait;
use Phalcon\Storage\SerializerFactory;
use FunctionalTester;

use function getOptionsSqlite;

class GetSetDefaultSerializerCest
{
    use DatabaseTrait;

    /**
     * Tests Phalcon\Incubator\Cache\Database ::
     * getDefaultSerializer()/setDefaultSerializer()
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2019-04-13
     */
    public function storageAdapterRedisGetKeys(FunctionalTester $I)
    {
        $I->wantToTest('Cache\Adapter\Database - getDefaultSerializer()/setDefaultSerializer()');

        $serializer = new SerializerFactory();

        $adapter = new Database(
            $serializer,
            $this->getOptions()
        );

        $I->assertEquals(
            'Php',
            $adapter->getDefaultSerializer()
        );

        $adapter->setDefaultSerializer('Base64');

        $I->assertEquals(
            'Base64',
            $adapter->getDefaultSerializer()
        );
    }
}
