<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\FabricantesTable;
use Authorization\IdentityInterface;

final class FabricantesTablePolicy
{
    use PolicyTrait;

    /** Check permission to list manufacturers. */
    public function canIndex(IdentityInterface $identity, FabricantesTable $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.visualizar');
    }

    /** Check permission to create manufacturers. */
    public function canAdd(IdentityInterface $identity, FabricantesTable $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.criar');
    }
}
