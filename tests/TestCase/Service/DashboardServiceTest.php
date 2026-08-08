<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Service\DashboardService;
use Cake\I18n\Date;
use Cake\TestSuite\TestCase;

final class DashboardServiceTest extends TestCase
{
    protected array $fixtures = [
        'app.Medicamentos', 'app.Produtos', 'app.Lotes', 'app.Vendas', 'app.Compras',
        'app.Pacientes', 'app.Consultas',
    ];

    /** Build all management aggregates for an empty, bounded period. */
    public function testIndicadoresGerenciais(): void
    {
        $indicadores = (new DashboardService())->indicadores(new Date('2030-01-01'), new Date('2030-01-31'));

        $this->assertArrayHasKey('valor_estoque', $indicadores);
        $this->assertArrayHasKey('consultas_realizadas', $indicadores);
        $this->assertSame(0, $indicadores['vendas_periodo']);
    }
}
