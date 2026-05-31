<?php

namespace app\models\vo;

class CitaVo
{
    public ?int $idCita;
    public ?int $idPaciente;
    public ?int $idProfesional;
    public ?string $fechaCita;
    public ?string $horaCita;
    public ?string $estado;
    public ?string $observaciones;
    public ?string $fechaHoraEstimada;

    public function __construct(
        ?int $idCita = null,
        ?int $idPaciente = null,
        ?int $idProfesional = null,
        ?string $fechaCita = null,
        ?string $horaCita = null,
        ?string $estado = null,
        ?string $observaciones = null,
        ?string $fechaHoraEstimada = null
    ) {
        $this->idCita = $idCita;
        $this->idPaciente = $idPaciente;
        $this->idProfesional = $idProfesional;
        $this->fechaCita = $fechaCita;
        $this->horaCita = $horaCita;
        $this->estado = $estado;
        $this->observaciones = $observaciones;
        $this->fechaHoraEstimada = $fechaHoraEstimada;
    }

    public function getIdCita(): ?int
    {
        return $this->idCita;
    }

    public function setIdCita(?int $idCita): void
    {
        $this->idCita = $idCita;
    }

    public function getIdPaciente(): ?int
    {
        return $this->idPaciente;
    }

    public function setIdPaciente(?int $idPaciente): void
    {
        $this->idPaciente = $idPaciente;
    }

    public function getIdProfesional(): ?int
    {
        return $this->idProfesional;
    }

    public function setIdProfesional(?int $idProfesional): void
    {
        $this->idProfesional = $idProfesional;
    }

    public function getFechaCita(): ?string
    {
        return $this->fechaCita;
    }

    public function setFechaCita(?string $fechaCita): void
    {
        $this->fechaCita = $fechaCita;
    }

    public function getHoraCita(): ?string
    {
        return $this->horaCita;
    }

    public function setHoraCita(?string $horaCita): void
    {
        $this->horaCita = $horaCita;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(?string $estado): void
    {
        $this->estado = $estado;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): void
    {
        $this->observaciones = $observaciones;
    }

    public function getFechaHoraEstimada(): ?string
    {
        return $this->fechaHoraEstimada;
    }

    public function setFechaHoraEstimada(?string $fechaHoraEstimada): void
    {
        $this->fechaHoraEstimada = $fechaHoraEstimada;
    }
}
