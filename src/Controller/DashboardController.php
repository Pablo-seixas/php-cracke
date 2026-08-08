<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\DashboardService;
use Cake\I18n\Date;

final class DashboardController extends AppController
{
    /** Display filterable management indicators and charts. */
    public function index(): void
    {
        $this->Authorization->skipAuthorization();
        $inicio = $this->data('inicio', 'first day of this month');
        $fim = $this->data('fim', 'last day of this month');
        $service = new DashboardService();
        $this->set('indicadores', $service->indicadores($inicio, $fim));
        $this->set('vendasPorDia', $service->vendasPorDia($inicio, $fim));
    }

    /** Parse a date filter using a safe fallback. */
    private function data(string $campo, string $padrao): Date
    {
        $valor = (string)$this->request->getQuery($campo);

        return new Date($valor !== '' ? $valor : $padrao);
    }
}
