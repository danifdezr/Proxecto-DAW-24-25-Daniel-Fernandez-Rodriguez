<?php

namespace app\models\vo;

class PacienteVo
{
    public ?int $idPaciente;
    public ?string $nombre;
    public ?string $apellidos;
    public ?string $email;
    public ?string $telefono;
    public ?string $dni;
    public ?string $fechaNacimiento;

    public function __construct(
        ?int $idPaciente = null,
        ?string $nombre = null,
        ?string $apellidos = null,
        ?string $email = null,
        ?string $telefono = null,
        ?string $dni = null,
        ?string $fechaNacimiento = null
    ) {
        $this->idPaciente = $idPaciente;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->email = $email;
        $this->telefono = $telefono;
        $this->dni = $dni;
        $this->fechaNacimiento = $fechaNacimiento;
    }

    public function getIdPaciente(): ?int
    {
        return $this->idPaciente;
    }

    public function setIdPaciente(?int $idPaciente): void
    {
        $this->idPaciente = $idPaciente;
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

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function setDni(?string $dni): void
    {
        $this->dni = $dni;
    }

    public function getFechaNacimiento(): ?string
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento(?string $fechaNacimiento): void
    {
        $this->fechaNacimiento = $fechaNacimiento;
    }
}