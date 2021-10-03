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

class DecrementCest
{
    use DatabaseTrait;

    /**
     * Tests Phalcon\Incubator\Cache\Aerospike :: decrement()
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2021-10-03
     */
    public function cacheAdapterAerospikeDecrement(FunctionalTester $I)
    {
        $I->wantToTest('Cache\Adapter\Aerospike - decrement()');
    }

    /**
     * Tests Phalcon\Incubator\Cache\Aerospike :: decrement() - twice
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2021-10-03
     */
    public function cacheAdapterAerospikeDecrementTwice(FunctionalTester $I)
    {
        $I->wantToTest('Cache\Adapter\Database - decrement() - twice');
    }
}
