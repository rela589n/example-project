<?php

declare(strict_types=1);

namespace App\Playground\AmPHP\File;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Amp\ByteStream\getOutputBufferStream;
use function Amp\ByteStream\pipe;
use function Amp\File\filesystem;

#[AsCommand('app:amphp:pipe-file')]
final class PipeFileConsoleCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = filesystem()->openFile(__DIR__.'/lorem.txt', 'rb');

        // php://output is basically the same as using echo
        pipe($file, getOutputBufferStream());

        return 0;
    }
}
