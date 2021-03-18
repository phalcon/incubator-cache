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

use Codeception\Example;
use Phalcon\Incubator\Cache\Database;
use Phalcon\Storage\Exception;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Incubator\Cache\Test\Fixtures\Traits\Cache\DatabaseTrait;
use stdClass;
use FunctionalTester;

use function array_merge;
use function getOptionsSqlite;
use function uniqid;

class GetSetCest
{
    use DatabaseTrait;

    /**
     * Tests Phalcon\Incubator\Cache\Database :: get()
     *
     * @dataProvider getExamples
     *
     * @throws Exception
     * @since        2019-03-31
     *
     * @author       Phalcon Team <team@phalcon.io>
     */
    public function cacheAdapterDatabaseGetSet(FunctionalTester $I, Example $example)
    {
        $I->wantToTest('Cache\Adapter\Database - get()/set() - ' . $example[0]);

        $serializer = new SerializerFactory();
        $adapter    = new Database($serializer, $this->getOptions());

        $key = 'cache-data';

        $result = $adapter->set($key, $example[1]);
        $I->assertTrue($result);

        $expected = $example[1];
        $actual   = $adapter->get($key);
        $I->assertEquals($expected, $actual);
    }

    /**
     * Tests Phalcon\Incubator\Cache\Database :: get()/set() - custom serializer
     *
     * @throws Exception
     * @since  2019-04-29
     *
     * @author Phalcon Team <team@phalcon.io>
     */
    public function cacheAdapterDatabaseGetSetCustomSerializer(FunctionalTester $I)
    {
        $I->wantToTest('Cache\Adapter\Database - get()/set() - custom serializer');

        $serializer = new SerializerFactory();

        $adapter = new Database(
            $serializer,
            array_merge(
                $this->getOptions(),
                [
                    'defaultSerializer' => 'Base64',
                ]
            )
        );

        $key    = 'cache-data';
        $source = 'Phalcon Framework';

        $I->assertTrue(
            $adapter->set($key, $source)
        );


        $I->assertEquals(
            $source,
            $adapter->get($key)
        );
    }

    private function getExamples(): array
    {
        return [
            [
                'string',
                'random string',
            ],
            [
                'integer',
                123456,
            ],
            [
                'float',
                123.456,
            ],
            [
                'boolean',
                true,
            ],
            [
                'object',
                new stdClass(),
            ],
        ];
    }
}
