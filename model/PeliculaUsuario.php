<?php

class PeliculaUsuario
{
    private ?int $idPeliculaUsuario;
    private int $idPelicula;
    private int $idUsuario;
    private string $tipo;

    public function __construct(
        int $idPelicula,
        int $idUsuario,
        string $tipo,
        ?int $idPeliculaUsuario = null
    ) {
        $this->idPeliculaUsuario = $idPeliculaUsuario;
        $this->idPelicula = $idPelicula;
        $this->idUsuario = $idUsuario;
        $this->tipo = $tipo;
    }

    public function getIdPeliculaUsuario(): ?int
    {
        return $this->idPeliculaUsuario;
    }

    public function getIdPelicula(): int
    {
        return $this->idPelicula;
    }

    public function getIdUsuario(): int
    {
        return $this->idUsuario;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): void
    {
        $this->tipo = $tipo;
    }
}
