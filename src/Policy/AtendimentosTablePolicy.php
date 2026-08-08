<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\AtendimentosTable;
use Authorization\IdentityInterface;

final class AtendimentosTablePolicy
{
    use PolicyTrait;

    /** Browse encounters. */
    public function canIndex(IdentityInterface $identity, AtendimentosTable $resource): bool
    {
        return $this->permitido($identity, 'prontuarios.visualizar');
    }

    /** Start an encounter. */
    public function canAdd(IdentityInterface $identity, AtendimentosTable $resource): bool
    {
        return $this->permitido($identity, 'prontuarios.editar');
    }
}
