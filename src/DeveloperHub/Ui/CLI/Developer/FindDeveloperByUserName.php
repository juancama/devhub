<?php
declare(strict_types=1);

namespace Jcv\DeveloperHub\Ui\CLI\Developer;

use Jcv\DeveloperHub\Application\Developer\FindDeveloperByUserNameQuery;
use Jcv\DeveloperHub\Application\Developer\FindDeveloperByUserNameUseCase;
use Jcv\Shared\Bus\Query\QueryBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FindDeveloperByUserName extends Command
{
    protected static $defaultName = 'colvin:search-developer';

    private QueryBus $useCase;

    public function __construct(QueryBus $queryBus)
    {
        $this->useCase = $queryBus;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Search developer by username.')
            ->setHelp('Find developer by username on github.')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(['']);

        $username = $input->getArgument('username');

        $output->writeln(["> Searching: {$username}"]);

        //todo: handle query exceptions and print error message
        $developer = $this->useCase->ask(
            new FindDeveloperByUserNameQuery($username)
        );

        $result = $developer ? json_encode($developer->payload(), JSON_PRETTY_PRINT) : 'Not found';

        $output->writeln(['', $result. '', '']);

        return Command::SUCCESS;
    }
}
