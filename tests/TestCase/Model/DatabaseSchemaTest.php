<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

final class DatabaseSchemaTest extends TestCase
{
    private const REQUIRED_TABLES = [
        'agendas', 'atendimentos', 'auditorias', 'caixas', 'categorias', 'clientes', 'compras',
        'compras_itens', 'configuracoes', 'consultas', 'contas_pagar', 'contas_receber', 'cotacoes',
        'cotacoes_itens', 'especialidades', 'fabricantes', 'fornecedores', 'fornecedores_produtos',
        'lotes', 'medicamentos', 'movimentacoes_caixa', 'movimentacoes_estoque', 'notificacoes',
        'pacientes', 'pagamentos', 'perfis', 'perfis_permissoes', 'permissoes', 'prescricoes',
        'produtos', 'profissionais', 'prontuarios', 'prontuarios_historicos', 'receitas',
        'solicitacoes_compras', 'solicitacoes_compras_itens', 'tentativas_login', 'usuarios',
        'usuarios_perfis', 'vendas', 'vendas_itens',
    ];

    public function testAllRequiredTablesExist(): void
    {
        $connection = ConnectionManager::get('test');
        $rows = $connection->execute(
            "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'",
        )->fetchAll('assoc');
        $tables = array_column($rows, 'table_name');

        foreach (self::REQUIRED_TABLES as $table) {
            $this->assertContains($table, $tables, "Tabela obrigatória ausente: {$table}");
        }
    }

    public function testAccessControlAssociationsUsePortugueseForeignKeys(): void
    {
        $usuarios = TableRegistry::getTableLocator()->get('Usuarios');
        $perfis = TableRegistry::getTableLocator()->get('Perfis');
        $permissoes = TableRegistry::getTableLocator()->get('Permissoes');

        $this->assertSame('perfil_id', $usuarios->Perfis->getTargetForeignKey());
        $this->assertSame('perfil_id', $perfis->Permissoes->getForeignKey());
        $this->assertSame('permissao_id', $permissoes->Perfis->getForeignKey());
    }

    public function testCriticalColumnsUseExpectedDatabaseTypes(): void
    {
        $connection = ConnectionManager::get('test');
        $rows = $connection->execute(<<<'SQL'
SELECT table_name, column_name, data_type
FROM information_schema.columns
WHERE table_schema = 'public'
  AND (table_name, column_name) IN (
      ('medicamentos', 'preco_venda'),
      ('lotes', 'data_validade'),
      ('auditorias', 'dados_posteriores'),
      ('movimentacoes_estoque', 'endereco_ip')
  )
SQL)->fetchAll('assoc');
        $types = [];
        foreach ($rows as $row) {
            $types[$row['table_name'] . '.' . $row['column_name']] = $row['data_type'];
        }

        $this->assertSame('numeric', $types['medicamentos.preco_venda'] ?? null);
        $this->assertSame('date', $types['lotes.data_validade'] ?? null);
        $this->assertSame('jsonb', $types['auditorias.dados_posteriores'] ?? null);
        $this->assertSame('inet', $types['movimentacoes_estoque.endereco_ip'] ?? null);
    }
}
