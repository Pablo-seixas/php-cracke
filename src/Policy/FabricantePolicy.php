<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Fabricante;
use Authorization\IdentityInterface;

final class FabricantePolicy
{
    use PolicyTrait;

    /** Check permission to edit manufacturers. */
    public function canEdit(IdentityInterface $identity, Fabricante $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.editar');
    }

    /** Check permission to inactivate manufacturers. */
    public function canInativar(IdentityInterface $identity, Fabricante $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.editar');
    }
}
