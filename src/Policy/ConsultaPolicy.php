<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Consulta;
use Authorization\IdentityInterface;

final class ConsultaPolicy
{
    use PolicyTrait;

    /** Update consultation status. */
    public function canEdit(IdentityInterface $identity, Consulta $resource): bool
    {
        return $this->permitido($identity, 'agenda.criar');
    }
}
