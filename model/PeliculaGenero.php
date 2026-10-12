<?php

class PeliculaGenero
{
    private Pelicula $pelicula;
    private Genero $genero;

    public function __construct(Pelicula $pelicula, Genero $genero)
    {
        $this->pelicula = $pelicula;
        $this->genero = $genero;
    }

    public function getPelicula(): Pelicula
    {
        return $this->pelicula;
    }

    public function getGenero(): Genero
    {
        return $this->genero;
    }
}
