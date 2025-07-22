<?php

declare(strict_types=1);

namespace App\Support\CycleBridge\DBAL;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Config\Postgres\TcpConnectionConfig;
use Cycle\Database\Config\PostgresDriverConfig;
use Cycle\Database\DatabaseManager;

final readonly class DatabaseManagerFactory
{
    public function createDatabaseManager(): DatabaseManager
    {
        $config = new DatabaseConfig([
            'default' => 'default',
            'databases' => [
                'default' => [
                    'connection' => 'postgres',
                ],
            ],
            'connections' => [
                'postgres' => new PostgresDriverConfig(
                    connection: new TcpConnectionConfig(
                        database: 'project_db',
                        host: '127.0.0.1',
                        port: 15432,
                        user: 'postgres',
                        password: 'qwerty',
                    ),
                    schema: 'public',
                    queryCache: true,
                ),
            ],
        ]);

        return new DatabaseManager($config);
    }
}
