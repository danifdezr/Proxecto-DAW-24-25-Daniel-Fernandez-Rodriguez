<?php

namespace app\models\vo;

class UsuarioVo
{
    public ?int $idUsuario;
    public ?string $nombre;
    public ?string $apellidos;
    public ?string $email;
    public ?string $telefono;
    public ?string $contrasenaHash;
    public ?string $rol;
    public ?string $fechaAlta;
    public ?bool $activo;

    public function __construct(
        ?int $idUsuario = null,
        ?string $nombre = null,
        ?string $apellidos = null,
        ?string $email = null,
        ?string $telefono = null,
        ?string $contrasenaHash = null,
        ?string $rol = null,
        ?string $fechaAlta = null,
        ?bool $activo = null
    ) {
        $this->idUsuario = $idUsuario;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->email = $email;
        $this->telefono = $telefono;
        $this->contrasenaHash = $contrasenaHash;
        $this->rol = $rol;
        $this->fechaAlta = $fechaAlta;
        $this->activo = $activo;
    }

    public function getIdUsuario(): ?int
    {
        return $this->idUsuario;
    }

    public function setIdUsuario(?int $idUsuario): void
    {
        $this->idUsuario = $idUsuario;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(?string $apellidos): void
    {
        $this->apellidos = $apellidos;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): void
    {
        $this->telefono = $telefono;
    }

    public function getContrasenaHash(): ?string
    {
        return $this->contrasenaHash;
    }

    public function setContrasenaHash(?string $contrasenaHash): void
    {
        $this->contrasenaHash = $contrasenaHash;
    }

    public function getRol(): ?string
    {
        return $this->rol;
    }

    public function setRol(?string $rol): void
    {
        $this->rol = $rol;
    }

    public function getFechaAlta(): ?string
    {
        return $this->fechaAlta;
    }

    public function setFechaAlta(?string $fechaAlta): void
    {
        $this->fechaAlta = $fechaAlta;
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
