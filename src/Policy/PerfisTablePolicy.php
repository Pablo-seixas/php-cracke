<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Table\PerfisTable;
use Authorization\IdentityInterface;

final class PerfisTablePolicy
{
    use PolicyTrait;

    /** Check permission to list profiles. */
    public function canIndex(IdentityInterface $identity, PerfisTable $resource): bool
    {
        return $this->permitido($identity, 'usuarios.gerenciar');
    }
}
