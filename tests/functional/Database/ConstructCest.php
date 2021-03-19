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

use Phalcon\Cache\Adapter\AdapterInterface;
use Phalcon\Incubator\Cache\Database;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Incubator\Cache\Test\Fixtures\Traits\Cache\DatabaseTrait;
use FunctionalTester;

use function getOptionsSqlite;

class ConstructCest
{
    use DatabaseTrait;

    /**
     * Tests Phalcon\Incubator\Cache\Database :: __construct()
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2019-04-09
     */
    public function cacheAdapterDatabaseConstruct(FunctionalTester $I)
    {
        $I->wantToTest('Cache\Adapter\Database - __construct()');

        $serializer = new SerializerFactory();
        $adapter    = new Database($serializer, $this->getOptions());

        $class = Database::class;
        $I->assertInstanceOf($class, $adapter);

        $class = AdapterInterface::class;
        $I->assertInstanceOf($class, $adapter);
    }
}
