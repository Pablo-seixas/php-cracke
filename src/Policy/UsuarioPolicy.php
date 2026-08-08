<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Usuario;
use Authorization\IdentityInterface;

final class UsuarioPolicy
{
    use PolicyTrait;

    /** Check permission to view a user. */
    public function canView(IdentityInterface $identity, Usuario $resource): bool
    {
        $usuario = $this->usuario($identity);

        return $usuario?->id === $resource->id || $this->permitido($identity, 'usuarios.gerenciar');
    }

    /** Check permission to edit a user. */
    public function canEdit(IdentityInterface $identity, Usuario $resource): bool
    {
        return $this->permitido($identity, 'usuarios.gerenciar');
    }

    /** Check permission to inactivate another user. */
    public function canInativar(IdentityInterface $identity, Usuario $resource): bool
    {
        $usuario = $this->usuario($identity);

        return $usuario?->id !== $resource->id && $this->permitido($identity, 'usuarios.gerenciar');
    }

    /** Allow only the master administrator to unlock accounts. */
    public function canDesbloquear(IdentityInterface $identity, Usuario $resource): bool
    {
        return $this->usuario($identity)?->email === 'admin@farmaclinic.local';
    }
}
