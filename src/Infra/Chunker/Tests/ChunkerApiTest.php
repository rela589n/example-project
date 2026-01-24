<?php

declare(strict_types=1);

namespace App\Infra\Chunker\Tests;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

#[CoversNothing]
final class ChunkerApiTest extends TestCase
{
    private const string CHUNKER_URL = 'http://chunker:8000';

    public function testChunkNeuralEndpoint(): void
    {
        $client = HttpClient::create();

        $response = $client->request('POST', self::CHUNKER_URL.'/chunk/neural', [
            'json' => [
                'content' => file_get_contents(__DIR__.'/trees.txt'),
                'model' => 'mirth/chonky_modernbert_base_1',
                'min_characters_per_chunk' => 24,
            ],
        ]);

        self::assertSame(200, $response->getStatusCode());

        /** @var array{chunks: list<array{index: int, content: string, token_count: int}>} $data */
        $data = $response->toArray();

        self::assertSame([
            'chunks' => [
                [
                    'index' => 0,
                    'content' => file_get_contents(__DIR__.'/chunks/0.txt'),
                    'token_count' => 70,
                ],
                [
                    'index' => 1,
                    'content' => file_get_contents(__DIR__.'/chunks/1.txt'),
                    'token_count' => 102,
                ],
                [
                    'index' => 2,
                    'content' => file_get_contents(__DIR__.'/chunks/2.txt'),
                    'token_count' => 79,
                ],
                [
                    'index' => 3,
                    'content' => file_get_contents(__DIR__.'/chunks/3.txt'),
                    'token_count' => 85,
                ],
                [
                    'index' => 4,
                    'content' => file_get_contents(__DIR__.'/chunks/4.txt'),
                    'token_count' => 56,
                ],
                [
                    'index' => 5,
                    'content' => file_get_contents(__DIR__.'/chunks/5.txt'),
                    'token_count' => 70,
                ],
                [
                    'index' => 6,
                    'content' => file_get_contents(__DIR__.'/chunks/6.txt'),
                    'token_count' => 55,
                ],
                [
                    'index' => 7,
                    'content' => file_get_contents(__DIR__.'/chunks/7.txt'),
                    'token_count' => 57,
                ],
                [
                    'index' => 8,
                    'content' => file_get_contents(__DIR__.'/chunks/8.txt'),
                    'token_count' => 57,
                ],
                [
                    'index' => 9,
                    'content' => file_get_contents(__DIR__.'/chunks/9.txt'),
                    'token_count' => 65,
                ],
                [
                    'index' => 10,
                    'content' => file_get_contents(__DIR__.'/chunks/10.txt'),
                    'token_count' => 114,
                ],
                [
                    'index' => 11,
                    'content' => file_get_contents(__DIR__.'/chunks/11.txt'),
                    'token_count' => 111,
                ],
                [
                    'index' => 12,
                    'content' => file_get_contents(__DIR__.'/chunks/12.txt'),
                    'token_count' => 70,
                ],
                [
                    'index' => 13,
                    'content' => file_get_contents(__DIR__.'/chunks/13.txt'),
                    'token_count' => 97,
                ],
                [
                    'index' => 14,
                    'content' => file_get_contents(__DIR__.'/chunks/14.txt'),
                    'token_count' => 141,
                ],
                [
                    'index' => 15,
                    'content' => file_get_contents(__DIR__.'/chunks/15.txt'),
                    'token_count' => 86,
                ],
                [
                    'index' => 16,
                    'content' => file_get_contents(__DIR__.'/chunks/16.txt'),
                    'token_count' => 62,
                ],
                [
                    'index' => 17,
                    'content' => file_get_contents(__DIR__.'/chunks/17.txt'),
                    'token_count' => 146,
                ],
                [
                    'index' => 18,
                    'content' => file_get_contents(__DIR__.'/chunks/18.txt'),
                    'token_count' => 127,
                ],
                [
                    'index' => 19,
                    'content' => file_get_contents(__DIR__.'/chunks/19.txt'),
                    'token_count' => 101,
                ],
                [
                    'index' => 20,
                    'content' => file_get_contents(__DIR__.'/chunks/20.txt'),
                    'token_count' => 96,
                ],
            ],
        ], $data);
    }
}
