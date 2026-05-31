<?php

namespace app\models\vo;

class NotificacionVo
{
    public ?int $idNotificacion;
    public ?int $idCita;
    public ?string $tipo;
    public ?string $mensaje;
    public ?string $fechaEnvio;
    public ?string $estado;

    public function __construct(
        ?int $idNotificacion = null,
        ?int $idCita = null,
        ?string $tipo = null,
        ?string $mensaje = null,
        ?string $fechaEnvio = null,
        ?string $estado = null
    ) {
        $this->idNotificacion = $idNotificacion;
        $this->idCita = $idCita;
        $this->tipo = $tipo;
        $this->mensaje = $mensaje;
        $this->fechaEnvio = $fechaEnvio;
        $this->estado = $estado;
    }

    public function getIdNotificacion(): ?int
    {
        return $this->idNotificacion;
    }

    public function setIdNotificacion(?int $idNotificacion): void
    {
        $this->idNotificacion = $idNotificacion;
    }

    public function getIdCita(): ?int
    {
        return $this->idCita;
    }

    public function setIdCita(?int $idCita): void
    {
        $this->idCita = $idCita;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): void
    {
        $this->tipo = $tipo;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function setMensaje(?string $mensaje): void
    {
        $this->mensaje = $mensaje;
    }

    public function getFechaEnvio(): ?string
    {
        return $this->fechaEnvio;
    }

    public function setFechaEnvio(?string $fechaEnvio): void
    {
        $this->fechaEnvio = $fechaEnvio;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(?string $estado): void
    {
        $this->estado = $estado;
    }
}
