<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Especialidade;
use Authorization\IdentityInterface;

final class EspecialidadePolicy
{
    use PolicyTrait;

    /** Edit specialties. */
    public function canEdit(IdentityInterface $identity, Especialidade $resource): bool
    {
        return $this->permitido($identity, 'agenda.criar');
    }
}
