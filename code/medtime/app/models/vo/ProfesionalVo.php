<?php

namespace app\models\vo;

class ProfesionalVo
{
    public ?int $idProfesional;
    public ?string $nombre;
    public ?string $apellidos;
    public ?string $email;
    public ?string $telefono;
    public ?string $especialidad;
    public ?bool $disponible;

    public function __construct(
        ?int $idProfesional = null,
        ?string $nombre = null,
        ?string $apellidos = null,
        ?string $email = null,
        ?string $telefono = null,
        ?string $especialidad = null,
        ?bool $disponible = null
    ) {
        $this->idProfesional = $idProfesional;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->email = $email;
        $this->telefono = $telefono;
        $this->especialidad = $especialidad;
        $this->disponible = $disponible;
    }

    public function getIdProfesional(): ?int
    {
        return $this->idProfesional;
    }

    public function setIdProfesional(?int $idProfesional): void
    {
        $this->idProfesional = $idProfesional;
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

    public function getEspecialidad(): ?string
    {
        return $this->especialidad;
    }

    public function setEspecialidad(?string $especialidad): void
    {
        $this->especialidad = $especialidad;
    }

    public function getDisponible(): ?bool
    {
        return $this->disponible;
    }

    public function setDisponible(?bool $disponible): void
    {
        $this->disponible = $disponible;
    }
}