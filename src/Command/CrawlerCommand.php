<?php

namespace App\Command;

use App\CrawlerService\Processor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CrawlerCommand extends Command
{
    protected static $defaultName = 'app:crawler';
    private $processor;

    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('To crawl URLs and extract emails');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->note(sprintf('Start to crawling'));
        $this->processor->execute();
        $io->success('Finished');
        return Command::SUCCESS;
    }
}
