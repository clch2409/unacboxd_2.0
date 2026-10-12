<?php

class Usuario
{
    private ?int $idUsuario;
    private string $nombreUsuario;
    private string $email;
    private string $contrasena;
    private string $preguntaSecreta;
    private ?Rol $rol;
    private array $peliculas = [];

    public function __construct(
        string $nombreUsuario,
        string $email,
        string $contrasena,
        string $preguntaSecreta,
        ?Rol $rol = null,
        ?int $idUsuario = null
    ) {
        $this->idUsuario = $idUsuario;
        $this->nombreUsuario = $nombreUsuario;
        $this->email = $email;
        $this->contrasena = $contrasena;
        $this->preguntaSecreta = $preguntaSecreta;
        $this->rol = $rol;
    }

    public function getIdUsuario(): ?int
    {
        return $this->idUsuario;
    }

    public function getNombreUsuario(): string
    {
        return $this->nombreUsuario;
    }

    public function setNombreUsuario(string $nombreUsuario): void
    {
        $this->nombreUsuario = $nombreUsuario;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getContrasena(): string
    {
        return $this->contrasena;
    }

    public function setContrasena(string $contrasena): void
    {
        $this->contrasena = $contrasena;
    }

    public function getPreguntaSecreta(): string
    {
        return $this->preguntaSecreta;
    }

    public function setPreguntaSecreta(string $preguntaSecreta): void
    {
        $this->preguntaSecreta = $preguntaSecreta;
    }

    public function getRol(): ?Rol
    {
        return $this->rol;
    }

    public function setRol(?Rol $rol): void
    {
        $this->rol = $rol;
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
