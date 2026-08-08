<?php
declare(strict_types=1);

namespace App\Validation;

final class DocumentoValidator
{
    /** Validate a Brazilian CPF, including both check digits. */
    public static function cpf(mixed $value): bool
    {
        $cpf = preg_replace('/\D/', '', (string)$value) ?? '';
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }
        for ($digito = 9; $digito < 11; $digito++) {
            $soma = 0;
            for ($indice = 0; $indice < $digito; $indice++) {
                $soma += (int)$cpf[$indice] * ($digito + 1 - $indice);
            }
            $verificador = (10 * $soma) % 11;
            $verificador = $verificador === 10 ? 0 : $verificador;
            if ((int)$cpf[$digito] !== $verificador) {
                return false;
            }
        }

        return true;
    }

    /** Validate a Brazilian CNPJ, including both check digits. */
    public static function cnpj(mixed $value): bool
    {
        $cnpj = preg_replace('/\D/', '', (string)$value) ?? '';
        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }
        $calcular = static function (string $base, array $pesos): int {
            $soma = 0;
            foreach ($pesos as $indice => $peso) {
                $soma += (int)$base[$indice] * $peso;
            }
            $resto = $soma % 11;

            return $resto < 2 ? 0 : 11 - $resto;
        };
        $primeiro = $calcular(substr($cnpj, 0, 12), [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);
        $segundo = $calcular(substr($cnpj, 0, 12) . $primeiro, [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);

        return $cnpj === substr($cnpj, 0, 12) . $primeiro . $segundo;
    }
}
