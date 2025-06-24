<?php

namespace App\Command;

use App\Service\EnergyService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-sample-data',
    description: 'Generate sample energy data for testing',
)]
class GenerateSampleDataCommand extends Command
{
    private EnergyService $energyService;

    public function __construct(EnergyService $energyService)
    {
        parent::__construct();
        $this->energyService = $energyService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Generating Sample Energy Data');
        $io->text('This will create sample energy consumption and production data...');

        try {
            $this->energyService->generateSampleData();
            
            $io->success('Sample data generated successfully!');
            $io->text('You can now view the dashboard with sample data.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Failed to generate sample data: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 