<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Review;
use App\Omdb\OmdbClient;
use App\Repository\MovieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    private $omdbClient;

    public function __construct(OmdbClient $omdbClient)
    {
        $this->omdbClient = $omdbClient;
    }

    /**
     * @Route("/movie/{id}", name="movie", requirements={"id": "tt\d+"})
     */
    public function details(string $id, MovieRepository $movieRepo): Response
    {
        $movie = $movieRepo->findOneBy(['imdbId' => $id]);
        if (!$movie) {
            $row = $this->omdbClient->requestById($id);
            $movie = new Movie();
            $movie->fromArray($row);
        }

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/movie/search/{movieName}", name="movie_top_rated", defaults={"movieName": "Harry Potter"})
     */
    public function search($movieName): Response
    {
        $rows = $this->omdbClient->requestBySearch($movieName);

        $movies = [];
        foreach ($rows['Search'] as $row) {
            $movie = new Movie();
            $movie->fromArray($row);
            $movies[] = $movie;
        }
        dump($movies, $rows);

        return $this->render('movie/search.html.twig', [
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
     * @Route("/movie/add/{imdbId}")
     */
    public function addMovie(string $imdbId,
                             EntityManagerInterface $em,
                             UserRepository $userRepository): Response
    {
        $row = $this->omdbClient->requestById($imdbId);
        $movie = new Movie();
        $movie->fromArray($row);

        $user = $userRepository->findOneBy(['id' => 1]);

        $review = new Review();
        $review->setRating(4);
        $review->setBody('Ce film est vraiment extraordinaire à voir absolument ;).');
        $review->setUser($user);
        $review->setMovie($movie);

        $em->persist($movie);
        $em->persist($review);
        $em->flush();

        return new Response('Movie added');
    }

    /**
    localhost:8002/api/movies
    [{
      'title': 'Title 1',
      'title': 'Title 2',
    }]
     */
}
