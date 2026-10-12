<?php

class Rol
{
    private ?int $idRol;
    private string $nombre;
    private array $usuarios = [];

    public function __construct(string $nombre, ?int $idRol = null)
    {
        $this->idRol = $idRol;
        $this->nombre = $nombre;
    }

    public function getIdRol(): ?int
    {
        return $this->idRol;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getUsuarios(): array
    {
        return $this->usuarios;
    }

    public function agregarUsuario(Usuario $usuario): void
    {
        $this->usuarios[] = $usuario;
    }
}
