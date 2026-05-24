<?php

namespace app\models\vo;

class UsuarioVo
{
    public ?int $id_usuario;
    public ?string $nome;
    public ?string $apelidos;
    public ?string $email;
    public ?string $telefono;
    public ?string $contrasinal_hash;
    public ?string $rol;
    public ?string $data_alta;
    public ?bool $activo;

    public function __construct(
        ?string $nome = null,
        ?string $apelidos = null,
        ?string $email = null,
        ?string $telefono = null,
        ?string $contrasinal_hash = null,
        ?string $rol = null,
        ?string $data_alta = null,
        ?bool $activo = null
    ) {
        $this->id_usuario = null;
        $this->nome = $nome;
        $this->apelidos = $apelidos;
        $this->email = $email;
        $this->telefono = $telefono;
        $this->contrasinal_hash = $contrasinal_hash;
        $this->rol = $rol;
        $this->data_alta = $data_alta;
        $this->activo = $activo;
    }

    public function getIdUsuario(): ?int
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(?int $id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): void
    {
        $this->nome = $nome;
    }

    public function getApelidos(): ?string
    {
        return $this->apelidos;
    }

    public function setApelidos(?string $apelidos): void
    {
        $this->apelidos = $apelidos;
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

    public function getContrasinalHash(): ?string
    {
        return $this->contrasinal_hash;
    }

    public function setContrasinalHash(?string $contrasinal_hash): void
    {
        $this->contrasinal_hash = $contrasinal_hash;
    }

    public function getRol(): ?string
    {
        return $this->rol;
    }

    public function setRol(?string $rol): void
    {
        $this->rol = $rol;
    }

    public function getDataAlta(): ?string
    {
        return $this->data_alta;
    }

    public function setDataAlta(?string $data_alta): void
    {
        $this->data_alta = $data_alta;
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