<?php

class PeliculaUsuario
{
    private Pelicula $pelicula;
    private Usuario $usuario;
    private string $tipo;
    private ?bool $activo;

    public function __construct(
        Pelicula $pelicula,
        Usuario $usuario,
        string $tipo,
        ?bool $activo = null
    ) {
        $this->pelicula = $pelicula;
        $this->usuario = $usuario;
        $this->tipo = $tipo;
        $this->activo = $activo;
    }

    public function getPelicula(): Pelicula
    {
        return $this->pelicula;
    }

    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): void
    {
        $this->tipo = $tipo;
    }

    public function getActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(?bool $activo): void
    {
        $this->activo = $activo;
    }
}
