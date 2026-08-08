<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\PacientesTable;
use Authorization\IdentityInterface;

final class PacientesTablePolicy
{
    use PolicyTrait;

    /** Browse patients. */
    public function canIndex(IdentityInterface $identity, PacientesTable $resource): bool
    {
        return $this->permitido($identity, 'pacientes.visualizar');
    }

    /** Create patients. */
    public function canAdd(IdentityInterface $identity, PacientesTable $resource): bool
    {
        return $this->permitido($identity, 'pacientes.editar');
    }
}
