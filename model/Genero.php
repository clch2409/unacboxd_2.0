<?php

class Genero
{
    private ?int $idGenero;
    private string $nombre;
    private array $peliculas = [];

    public function __construct(string $nombre, ?int $idGenero = null)
    {
        $this->idGenero = $idGenero;
        $this->nombre = $nombre;
    }

    public function getIdGenero(): ?int
    {
        return $this->idGenero;
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
