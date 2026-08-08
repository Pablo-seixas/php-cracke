<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Medicamento;
use Authorization\IdentityInterface;

final class MedicamentoPolicy
{
    use PolicyTrait;

    /** Check permission to view a medicine. */
    public function canView(IdentityInterface $identity, Medicamento $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.visualizar');
    }

    /** Check permission to edit a medicine. */
    public function canEdit(IdentityInterface $identity, Medicamento $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.editar');
    }

    /** Check permission to inactivate a medicine. */
    public function canInativar(IdentityInterface $identity, Medicamento $resource): bool
    {
        return $this->permitido($identity, 'medicamentos.editar');
    }
}
