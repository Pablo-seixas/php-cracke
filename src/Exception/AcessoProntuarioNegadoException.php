<?php
declare(strict_types=1);

namespace App\Exception;

use DomainException;

final class AcessoProntuarioNegadoException extends DomainException
{
    /** Build a safe, non-sensitive denial message. */
    public function __construct()
    {
        parent::__construct('Você não possui permissão para acessar este prontuário.');
    }
}
