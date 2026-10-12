<?php

class Director
{
    private ?int $idDirector;
    private string $nombre;
    private array $peliculas = [];

    public function __construct(string $nombre, ?int $idDirector = null)
    {
        $this->idDirector = $idDirector;
        $this->nombre = $nombre;
    }

    public function getIdDirector(): ?int
    {
        return $this->idDirector;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getPeliculas(): array
    {
        return $this->peliculas;
    }

    public function agregarPelicula(Pelicula $pelicula): void
    {
        $this->peliculas[] = $pelicula;
    }
}
