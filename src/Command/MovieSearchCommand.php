<?php

namespace App\Command;

use App\Omdb\OmdbClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MovieSearchCommand extends Command
{
    protected static $defaultName = 'app:movie:search';
    protected static $defaultDescription = 'Add a short description for your command';

    private $omdbClient;

    public function __construct(OmdbClient $omdbClient)
    {
        parent::__construct();
        $this->omdbClient = $omdbClient;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('keyword', InputArgument::OPTIONAL, 'Keyword to find movies')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$keyword = $input->getArgument('keyword')) {
            $keyword = $io->ask('Which movie you want to search?', 'Harry Potter', function($answer) {
                if (false !== strpos($answer, 'shit')) {
                    throw new \InvalidArgumentException($answer . ' is a word not allowed');
                }

                return $answer;
            });
        }

        $mediaType = $io->choice('What kind of media are you looking for?', ['movie', 'series', 'game', 'all']);

        $options = ('all' === $mediaType) ? [] : ['type' => $mediaType];
        $result = $this->omdbClient->requestBySearch($keyword, $options);
        $movies = $result['Search'];
        $io->progressStart(count($movies));

        $rows = [];
        foreach ($movies as $movie) {
            usleep(100000);
            $rows[] = [
                $movie['Title'],
                $movie['Year'],
                $movie['Type'],
                sprintf('https://www.imdb.com/title/%s/', $movie['imdbID'])
            ];
            $io->progressAdvance(1);
        }
        $output->write("/\r"); // \r should be escaped
        //$io->progressFinish();
        //dump($movies);

        $io->success(
            sprintf(' %s movies found for your search: "%s"', $result['totalResults'], $keyword)
        );

        $io->table(['Title', 'Year', 'Type', 'URL'], $rows);

        $io->block('Page 1/' . ceil($result['totalResults'] / count($movies)), 'INFO', 'fg=black;bg=blue', ' ', true);

        return 0;
    }
}
