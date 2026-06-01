<?php

namespace app\core;

class Validator
{
    private const LETRAS = 'TRWAGMYFPDXBNJZSQVHLCKE';

    /**
     * Valida un DNI o NIE.
     * Comprueba formato (8 dígitos + letra) y dígito de control por módulo 23.
     */
    public static function dniValido(string $dni): bool
    {
        $dni = strtoupper(trim($dni));

        // NIE: sustituir inicial X->0, Y->1, Z->2 para el cálculo del módulo
        if (preg_match('/^[XYZ]/', $dni)) {
            $mapa   = ['X' => '0', 'Y' => '1', 'Z' => '2'];
            $numero = $mapa[$dni[0]] . substr($dni, 1);
        } else {
            $numero = $dni;
        }

        // Formato válido: exactamente 8 dígitos seguidos de 1 letra
        if (!preg_match('/^\d{8}[A-Z]$/', $numero)) {
            return false;
        }

        return self::LETRAS[(int)substr($numero, 0, 8) % 23] === $numero[8];
    }
}
