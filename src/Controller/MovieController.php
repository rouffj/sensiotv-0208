<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Omdb\OmdbClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MovieController extends AbstractController
{
    private $omdbClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->omdbClient = new OmdbClient($httpClient, '28c5b7b1', 'https://www.omdbapi.com/');
    }

    /**
     * @Route("/movie/{id}", name="movie", requirements={"id": "tt\d+"})
     */
    public function details(string $id): Response
    {
        $row = $this->omdbClient->requestById($id);
        $movie = new Movie();
        $movie->fromArray($row);

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/movie/top-rated", name="movie_top_rated")
     */
    public function topRated(): Response
    {
        $rows = $this->omdbClient->requestBySearch('Lord of the rings');

        $movies = [];
        foreach ($rows['Search'] as $row) {
            $movie = new Movie();
            $movie->fromArray($row);
            $movies[] = $movie;
        }
        dump($movies, $rows);

        return $this->render('movie/top-rated.html.twig', [
            'movies' => $movies,
        ]);
    }

    /**
     * @Route("/movie/genres")
     */
    public function genres(): Response
    {
        return $this->render('movie/genres.html.twig', []);
    }

    /**
     * @Route("/movie/latest")
     */
    public function latest(): Response
    {
        return $this->render('movie/latest.html.twig', []);
    }

    /**
     * @Route("/api/movies", name="api_movie")
     */
    public function apiList(): Response
    {
        $movies = [
            ['title' => 'Movie 1'],
            ['title' => 'Movie 2'],
            ['title' => 'Movie 3'],
        ];

        $response = new Response(json_encode($movies), 200, [
            'Content-Type' => 'application/json'
        ]);

        return $response;
    }

    /**
    localhost:8002/api/movies
    [{
      'title': 'Title 1',
      'title': 'Title 2',
    }]
     */
}