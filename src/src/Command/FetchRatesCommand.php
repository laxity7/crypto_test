<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Provider\BinanceProvider;
use App\Task\FetchRatesTask;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name       : 'app:fetch-rates',
    description: 'Fetch currency rates from exchange provider',
)]
class FetchRatesCommand extends Command
{
    public function __construct(
        private readonly FetchRatesTask $fetchRatesTask,
        private readonly BinanceProvider $binanceProvider
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('provider', 'p', InputOption::VALUE_OPTIONAL, 'Exchange provider to use', 'binance')
            ->setHelp('This command allows you to fetch currency rates from specified exchange provider');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $providerName = $input->getOption('provider');
        if (!is_string($providerName)) {
            $providerName = 'unknown';
        }

        try {
            $provider = match ($providerName) {
                'binance' => $this->binanceProvider,
                default   => throw new \InvalidArgumentException(sprintf('Unknown provider: %s', $providerName))
            };
            $io->info(sprintf('Fetching rates using %s provider...', $providerName));

            $this->fetchRatesTask->handle($provider);
            $io->success('Rates fetched successfully!');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error(sprintf('Failed to fetch rates: %s', $e->getMessage()));

            return Command::FAILURE;
        }
    }
}
