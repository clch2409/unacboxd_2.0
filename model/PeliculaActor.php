<?php

class PeliculaActor
{
    private Pelicula $pelicula;
    private Actor $actor;

    public function __construct(Pelicula $pelicula, Actor $actor)
    {
        $this->pelicula = $pelicula;
        $this->actor = $actor;
    }

    public function getPelicula(): Pelicula
    {
        return $this->pelicula;
    }

    public function getActor(): Actor
    {
        return $this->actor;
    }
}
