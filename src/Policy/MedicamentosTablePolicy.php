<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\MedicamentosTable;
use Authorization\IdentityInterface;

final class MedicamentosTablePolicy
{
    use PolicyTrait;

    /** Check permission to browse medicines. */
    public function canIndex(IdentityInterface $identity, MedicamentosTable $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.visualizar');
    }

    /** Check permission to create medicines. */
    public function canAdd(IdentityInterface $identity, MedicamentosTable $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.criar');
    }
}
