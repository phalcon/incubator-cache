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

namespace Phalcon\Incubator\Cache\Tests\Functional\Aerospike;

use Phalcon\Incubator\Cache\Test\Fixtures\Traits\Cache\DatabaseTrait;
use FunctionalTester;

class ClearCest
{
    use DatabaseTrait;

    /**
     * Tests Phalcon\Incubator\Cache\Aerospike :: clear()
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2021-10-03
     */
    public function cacheAdapterAerospikeClear(FunctionalTester $I)
    {
        $I->wantToTest('Cache\Adapter\Aerospike - clear()');
    }

    /**
     * Tests Phalcon\Incubator\Cache\Aerospike :: clear() - twice
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2021-10-03
     */
    public function cacheAdapterAerospikeClearTwice(FunctionalTester $I)
    {
        $I->wantToTest('Cache\Adapter\Database - clear() - twice');
    }
}
