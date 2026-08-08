<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Atendimento;
use Authorization\IdentityInterface;

final class AtendimentoPolicy
{
    use PolicyTrait;

    /** View an encounter. */
    public function canView(IdentityInterface $identity, Atendimento $resource): bool
    {
        return $this->permitido($identity, 'prontuarios.visualizar');
    }

    /** Update an encounter. */
    public function canEdit(IdentityInterface $identity, Atendimento $resource): bool
    {
        return $this->permitido($identity, 'prontuarios.editar');
    }
}
