<?php

class Pelicula
{
    private ?int $idPelicula;
    private string $titulo;
    private int $anioEstreno;
    private int $duracionMinutos;
    private string $pais;
    private string $clasificacionEdad;
    private string $sinopsis;
    private ?string $poster;
    private ?string $color;
    private ?bool $fondoClaro;
    private Director $director;
    private array $generos = [];
    private array $actores = [];
    private array $calificaciones = [];

    public function __construct(
        string $titulo,
        int $anioEstreno,
        int $duracionMinutos,
        string $pais,
        string $clasificacionEdad,
        string $sinopsis,
        Director $director,
        ?string $poster = null,
        ?string $color = null,
        ?bool $fondoClaro = null,
        ?int $idPelicula = null
    ) {
        $this->idPelicula = $idPelicula;
        $this->titulo = $titulo;
        $this->anioEstreno = $anioEstreno;
        $this->duracionMinutos = $duracionMinutos;
        $this->pais = $pais;
        $this->clasificacionEdad = $clasificacionEdad;
        $this->sinopsis = $sinopsis;
        $this->director = $director;
        $this->poster = $poster;
        $this->color = $color;
        $this->fondoClaro = $fondoClaro;
    }

    public function getIdPelicula(): ?int
    {
        return $this->idPelicula;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): void
    {
        $this->titulo = $titulo;
    }

    public function getAnioEstreno(): int
    {
        return $this->anioEstreno;
    }

    public function getDuracionMinutos(): int
    {
        return $this->duracionMinutos;
    }

    public function getPais(): string
    {
        return $this->pais;
    }

    public function getClasificacionEdad(): string
    {
        return $this->clasificacionEdad;
    }

    public function getSinopsis(): string
    {
        return $this->sinopsis;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getFondoClaro(): ?bool
    {
        return $this->fondoClaro;
    }

    public function getDirector(): Director
    {
        return $this->director;
    }

    public function setDirector(Director $director): void
    {
        $this->director = $director;
    }

    public function getGeneros(): array
    {
        return $this->generos;
    }

    public function agregarGenero(Genero $genero): void
    {
        $this->generos[] = $genero;
    }

    public function getActores(): array
    {
        return $this->actores;
    }

    public function agregarActor(Actor $actor): void
    {
        $this->actores[] = $actor;
    }

    public function getCalificaciones(): array
    {
        return $this->calificaciones;
    }

    public function agregarCalificacion(Calificacion $calificacion): void
    {
        $this->calificaciones[] = $calificacion;
    }

    public function calcularPromedioCalificaciones(): ?float
    {
        $totalCalificaciones = count($this->calificaciones);
        if ($totalCalificaciones === 0) {
            return null;
        }

        $sumaCalificaciones = array_reduce($this->calificaciones, function ($suma, Calificacion $calificacion) {
            return $suma + $calificacion->getCalificacion();
        }, 0);

        return round($sumaCalificaciones / $totalCalificaciones, 2);
    }
}
