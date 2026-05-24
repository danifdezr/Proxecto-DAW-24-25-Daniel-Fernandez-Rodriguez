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
    public ?string $motivo;
    public ?string $horaEstimada;

    public function __construct(
        ?int $idCita = null,
        ?int $idPaciente = null,
        ?int $idProfesional = null,
        ?string $fechaCita = null,
        ?string $horaCita = null,
        ?string $estado = null,
        ?string $motivo = null,
        ?string $horaEstimada = null
    ) {
        $this->idCita = $idCita;
        $this->idPaciente = $idPaciente;
        $this->idProfesional = $idProfesional;
        $this->fechaCita = $fechaCita;
        $this->horaCita = $horaCita;
        $this->estado = $estado;
        $this->motivo = $motivo;
        $this->horaEstimada = $horaEstimada;
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

    public function getMotivo(): ?string
    {
        return $this->motivo;
    }

    public function setMotivo(?string $motivo): void
    {
        $this->motivo = $motivo;
    }

    public function getHoraEstimada(): ?string
    {
        return $this->horaEstimada;
    }

    public function setHoraEstimada(?string $horaEstimada): void
    {
        $this->horaEstimada = $horaEstimada;
    }
}