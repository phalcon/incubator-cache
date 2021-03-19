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

namespace Phalcon\Incubator\Cache\Test\Fixtures\Traits\Cache;

use FunctionalTester;
use Phalcon\Db\Adapter\Pdo\Sqlite;

trait DatabaseTrait
{
    public function _before(FunctionalTester $I)
    {
        $dbFile = env("DATA_SQLITE_CACHE_NAME", codecept_output_dir('cache.sqlite'));

        if (file_exists($dbFile)) {
            unlink($dbFile);
        }

        $connection = new Sqlite(getOptionsSqlite());

        $sql = <<<SQL
CREATE TABLE cache (
  `key_name` varchar(40) NOT NULL,
  `data` text,
  `lifetime` int(15) NOT NULL,
  PRIMARY KEY (`key_name`))
SQL;
        $connection->execute($sql);
    }

    public function getOptions()
    {
        return [
            "db" => new Sqlite(getOptionsSqlite()),
            "table" => "cache"
        ];
    }
}
