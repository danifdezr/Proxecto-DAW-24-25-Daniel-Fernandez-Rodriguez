<?php

namespace app\models\vo;

class ProfesionalVo
{
    public ?int    $idProfesional;
    public ?string $nombre;
    public ?string $apellidos;
    public ?string $email;
    public ?string $telefono;
    public ?string $especialidad;
    public ?bool   $disponible;
    public ?int    $duracionMediaConsultaMin;

    public function __construct(
        ?int    $idProfesional           = null,
        ?string $nombre                  = null,
        ?string $apellidos               = null,
        ?string $email                   = null,
        ?string $telefono                = null,
        ?string $especialidad            = null,
        ?bool   $disponible              = null,
        ?int    $duracionMediaConsultaMin = null
    ) {
        $this->idProfesional           = $idProfesional;
        $this->nombre                  = $nombre;
        $this->apellidos               = $apellidos;
        $this->email                   = $email;
        $this->telefono                = $telefono;
        $this->especialidad            = $especialidad;
        $this->disponible              = $disponible;
        $this->duracionMediaConsultaMin = $duracionMediaConsultaMin;
    }

    public function getIdProfesional(): ?int
    {
        return $this->idProfesional;
    }
    public function setIdProfesional(?int $v): void
    {
        $this->idProfesional = $v;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }
    public function setNombre(?string $v): void
    {
        $this->nombre = $v;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }
    public function setApellidos(?string $v): void
    {
        $this->apellidos = $v;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(?string $v): void
    {
        $this->email = $v;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }
    public function setTelefono(?string $v): void
    {
        $this->telefono = $v;
    }

    public function getEspecialidad(): ?string
    {
        return $this->especialidad;
    }
    public function setEspecialidad(?string $v): void
    {
        $this->especialidad = $v;
    }

    public function getDisponible(): ?bool
    {
        return $this->disponible;
    }
    public function setDisponible(?bool $v): void
    {
        $this->disponible = $v;
    }

    public function getDuracionMediaConsultaMin(): ?int
    {
        return $this->duracionMediaConsultaMin;
    }
    public function setDuracionMediaConsultaMin(?int $v): void
    {
        $this->duracionMediaConsultaMin = $v;
    }
}
