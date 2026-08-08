<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Datasource\ConnectionManager;
use Cake\I18n\Date;

final class DashboardService
{
    /** Return consolidated operational indicators for a period. */
    public function indicadores(?Date $inicio = null, ?Date $fim = null): array
    {
        $inicio ??= new Date('first day of this month');
        $fim ??= new Date('last day of this month');
        $db = ConnectionManager::get('default');
        $params = ['inicio' => $inicio->format('Y-m-d'), 'fim' => $fim->format('Y-m-d')];
        $estoqueCritico = 'SELECT COUNT(*) valor FROM medicamentos m WHERE m.ativo = TRUE '
            . 'AND (SELECT COALESCE(SUM(l.quantidade_disponivel), 0) FROM lotes l '
            . 'WHERE l.medicamento_id = m.id) <= m.estoque_minimo';
        $proximos = 'SELECT COUNT(*) valor FROM lotes WHERE quantidade_disponivel > 0 '
            . "AND data_validade BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL '90 days'";
        $vendas = "SELECT COALESCE(SUM(total), 0) valor FROM vendas WHERE status = 'finalizada' "
            . 'AND created::date BETWEEN :inicio AND :fim';
        $compras = 'SELECT COALESCE(SUM(valor_total), 0) valor FROM compras '
            . "WHERE status IN ('recebida','recebida_parcialmente') AND created::date BETWEEN :inicio AND :fim";
        $agendadas = "SELECT COUNT(*) valor FROM consultas WHERE status IN ('agendada','confirmada') "
            . 'AND inicio::date BETWEEN :inicio AND :fim';
        $realizadas = "SELECT COUNT(*) valor FROM consultas WHERE status = 'finalizada' "
            . 'AND inicio::date BETWEEN :inicio AND :fim';

        return [
            'medicamentos' => $this->valor($db, 'SELECT COUNT(*) valor FROM medicamentos WHERE ativo = TRUE'),
            'produtos' => $this->valor($db, 'SELECT COUNT(*) valor FROM produtos WHERE ativo = TRUE'),
            'sem_estoque' => $this->valor($db, 'SELECT COUNT(*) valor FROM lotes WHERE quantidade_disponivel <= 0'),
            'estoque_critico' => $this->valor($db, $estoqueCritico),
            'proximos_vencimento' => $this->valor($db, $proximos),
            'vencidos' => $this->valor(
                $db,
                'SELECT COUNT(*) valor FROM lotes WHERE quantidade_disponivel > 0 AND data_validade < CURRENT_DATE',
            ),
            'valor_estoque' => $this->valor(
                $db,
                'SELECT COALESCE(SUM(quantidade_disponivel * preco_custo), 0) valor FROM lotes',
            ),
            'vendas_periodo' => $this->valor($db, $vendas, $params),
            'compras_periodo' => $this->valor($db, $compras, $params),
            'pacientes' => $this->valor($db, 'SELECT COUNT(*) valor FROM pacientes WHERE ativo = TRUE'),
            'consultas_agendadas' => $this->valor($db, $agendadas, $params),
            'consultas_realizadas' => $this->valor($db, $realizadas, $params),
            'inicio' => $inicio, 'fim' => $fim,
        ];
    }

    /** Return daily sales series for charts. */
    public function vendasPorDia(Date $inicio, Date $fim): array
    {
        $sql = "SELECT created::date dia, SUM(total) total FROM vendas WHERE status = 'finalizada' "
            . 'AND created::date BETWEEN :inicio AND :fim GROUP BY created::date ORDER BY dia';

        return ConnectionManager::get('default')->execute($sql, [
            'inicio' => $inicio->format('Y-m-d'), 'fim' => $fim->format('Y-m-d'),
        ])->fetchAll('assoc');
    }

    /** Extract one aggregate value. */
    private function valor(object $db, string $sql, array $params = []): int|float
    {
        $valor = $db->execute($sql, $params)->fetch('assoc')['valor'] ?? 0;

        return is_numeric($valor) ? $valor + 0 : 0;
    }
}
