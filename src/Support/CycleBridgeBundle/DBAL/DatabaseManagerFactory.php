<?php

declare(strict_types=1);

namespace App\Support\CycleBridgeBundle\DBAL;

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
                ]
            ],
            'connections' => [
                'postgres' => new PostgresDriverConfig(
                    connection: new TcpConnectionConfig(
                        database: 'spiral',
                        host: '127.0.0.1',
                        port: 5432,
                        user: 'spiral',
                        password: '',
                    ),
                    schema: 'public',
                    queryCache: true,
                ),
            ],
        ]);

        return new DatabaseManager($config);
    }
}
