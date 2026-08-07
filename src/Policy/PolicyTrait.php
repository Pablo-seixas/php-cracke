<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Usuario;
use Authorization\IdentityInterface;

trait PolicyTrait
{
    /**
     * Extract the domain user from an authorization identity.
     */
    private function usuario(IdentityInterface $identity): ?Usuario
    {
        $data = $identity->getOriginalData();

        return $data instanceof Usuario ? $data : null;
    }

    /**
     * Check an explicit permission, with administrator override.
     */
    private function permitido(IdentityInterface $identity, string $permissao): bool
    {
        $usuario = $this->usuario($identity);

        return $usuario?->isAdministrador() === true || $usuario?->possuiPermissao($permissao) === true;
    }
}
