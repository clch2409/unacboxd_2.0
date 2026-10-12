<?php

class Calificacion
{
    private ?int $idCalificacion;
    private Pelicula $pelicula;
    private float $calificacion;

    public function __construct(
        Pelicula $pelicula,
        float $calificacion,
        ?int $idCalificacion = null
    ) {
        $this->idCalificacion = $idCalificacion;
        $this->pelicula = $pelicula;
        $this->calificacion = $calificacion;
    }

    public function getIdCalificacion(): ?int
    {
        return $this->idCalificacion;
    }

    public function getPelicula(): Pelicula
    {
        return $this->pelicula;
    }

    public function setPelicula(Pelicula $pelicula): void
    {
        $this->pelicula = $pelicula;
    }

    public function getCalificacion(): float
    {
        return $this->calificacion;
    }

    public function setCalificacion(float $calificacion): void
    {
        $this->calificacion = $calificacion;
    }
}
