<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MovieRepository::class)
 */
class Movie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $releaseDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $poster;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $imdbId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getImdbId(): ?string
    {
        return $this->imdbId;
    }

    public function setImdbId(string $imdbId): self
    {
        $this->imdbId = $imdbId;

        return $this;
    }

    public function fromArray(array $movie)
    {
        $this->releaseDate = \DateTime::createFromFormat('Y', $movie['Year']);
        $this->imdbId = $movie['imdbID'];
        $this->poster = $movie['Poster'];
        $this->title = $movie['Title'];
        $this->type = $movie['Type'];
    }

    public function fromCollection(array $rows)
    {
        $movies = [];
        foreach ($rows['Search'] as $row) {
            $movie = new Movie();
            $movie->fromArray($row);
            $movies[] = $movie;
        }

        return $movies;
    }
}
