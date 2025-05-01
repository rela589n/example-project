<?php

declare(strict_types=1);

namespace App\Playground\Autowire\Iterator\Vat\Console;

use App\Playground\Autowire\Iterator\Vat\VatCalculatorService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:check_vat', description: 'Check VAT service')]
class CheckVatConsoleCommand extends Command
{
    public function __construct(
        private readonly VatCalculatorService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('fop-group', InputArgument::REQUIRED, 'FOP group number');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $fopGroup = (int)$input->getArgument('fop-group');

        $vat = $this->service->calculate($fopGroup);

        $io->success(sprintf('VAT for FOP group %d is: %d', $fopGroup, $vat / 100));

        return Command::SUCCESS;
    }
}
