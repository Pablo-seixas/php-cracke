<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Paciente;
use Authorization\IdentityInterface;

final class PacientePolicy
{
    use PolicyTrait;

    /** View patients. */
    public function canView(IdentityInterface $identity, Paciente $resource): bool
    {
        return $this->permitido($identity, 'pacientes.visualizar');
    }

    /** Edit patients. */
    public function canEdit(IdentityInterface $identity, Paciente $resource): bool
    {
        return $this->permitido($identity, 'pacientes.editar');
    }

    /** Inactivate patients. */
    public function canInativar(IdentityInterface $identity, Paciente $resource): bool
    {
        return $this->permitido($identity, 'pacientes.editar');
    }
}
