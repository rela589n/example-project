<?php

declare(strict_types=1);

namespace App\Playground\Autowire\Iterator\Vat\Cli;

use App\Playground\Autowire\Iterator\Vat\FopGroup;
use App\Playground\Autowire\Iterator\Vat\VatCalculatorService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function sprintf;

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

        /** @var string $fopGroup */
        $fopGroup = $input->getArgument('fop-group');
        $fopGroup = (int)$fopGroup;

        $vat = $this->service->calculate(FopGroup::from($fopGroup));

        $io->success(sprintf('VAT for FOP group %d is: %d', $fopGroup, $vat / 100));

        return Command::SUCCESS;
    }
}
