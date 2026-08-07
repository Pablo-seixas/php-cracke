<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\UsuariosTable;
use Authorization\IdentityInterface;

final class UsuariosTablePolicy
{
    use PolicyTrait;

    /** Check permission to list users. */
    public function canIndex(IdentityInterface $identity, UsuariosTable $resource): bool
    {
        return $this->permitido($identity, 'usuarios.gerenciar');
    }

    /** Check permission to create users. */
    public function canAdd(IdentityInterface $identity, UsuariosTable $resource): bool
    {
        return $this->permitido($identity, 'usuarios.gerenciar');
    }
}
