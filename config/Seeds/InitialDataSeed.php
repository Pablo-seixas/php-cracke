<?php
declare(strict_types=1);

use Migrations\BaseSeed;

final class InitialDataSeed extends BaseSeed
{
    /**
     * Insert access-control and demonstration reference data.
     */
    public function run(): void
    {
        $profiles = [
            'Administrador', 'Farmacêutico', 'Médico', 'Enfermeiro', 'Atendente',
            'Estoquista', 'Comprador', 'Financeiro', 'Auditor', 'Consulta',
        ];
        $this->insertOrSkip('perfis', array_map(
            fn(string $profile): array => ['nome' => $profile, 'descricao' => "Perfil de acesso {$profile}"],
            $profiles,
        ));

        $permissions = [
            'medicamentos.visualizar', 'medicamentos.criar', 'medicamentos.editar',
            'estoque.visualizar', 'estoque.movimentar', 'compras.criar', 'compras.aprovar',
            'vendas.criar', 'vendas.cancelar', 'pacientes.visualizar', 'pacientes.editar',
            'agenda.visualizar', 'agenda.criar', 'prontuarios.visualizar', 'prontuarios.editar',
            'pagamentos.visualizar', 'relatorios.exportar', 'usuarios.gerenciar',
        ];
        $this->insertOrSkip('permissoes', array_map(
            fn(string $permission): array => ['codigo' => $permission, 'descricao' => "Permissão {$permission}"],
            $permissions,
        ));

        $this->execute(<<<'SQL'
INSERT INTO perfis_permissoes (perfil_id, permissao_id)
SELECT p.id, pe.id FROM perfis p CROSS JOIN permissoes pe WHERE p.nome = 'Administrador'
ON CONFLICT DO NOTHING
SQL);

        $this->execute(<<<'SQL'
INSERT INTO usuarios (nome, email, senha)
VALUES ('Administrador de Demonstração', 'admin@farmaclinic.local', '$2y$10$SQDp6YOVtjK9ykjnMjaArexfwxGRTJFUDAbhlGJqiVWPZuIMOHjmS')
ON CONFLICT (email) DO NOTHING
SQL);

        $this->execute(<<<'SQL'
INSERT INTO usuarios_perfis (usuario_id, perfil_id)
SELECT u.id, p.id FROM usuarios u CROSS JOIN perfis p
WHERE u.email = 'admin@farmaclinic.local' AND p.nome = 'Administrador'
ON CONFLICT DO NOTHING
SQL);

        $catalogStatements = [
            "INSERT INTO categorias (nome, descricao) VALUES ('Medicamentos', 'Medicamentos de demonstração') ON CONFLICT (nome) DO NOTHING",
            "INSERT INTO categorias (nome, descricao) VALUES ('Higiene', 'Produtos de higiene e cuidado') ON CONFLICT (nome) DO NOTHING",
            "INSERT INTO fabricantes (nome) VALUES ('Laboratório Exemplo') ON CONFLICT (nome) DO NOTHING",
            "INSERT INTO especialidades (nome, descricao) VALUES ('Clínica Geral', 'Especialidade fictícia para demonstração') ON CONFLICT (nome) DO NOTHING",
            "INSERT INTO configuracoes (chave, valor, descricao) VALUES ('estoque.dias_alerta_validade', '90', 'Dias para alertar vencimentos') ON CONFLICT (chave) DO NOTHING",
        ];

        foreach ($catalogStatements as $statement) {
            $this->execute($statement);
        }

        $this->execute(<<<'SQL'
INSERT INTO medicamentos (
    categoria_id, fabricante_id, codigo_interno, codigo_barras, nome_comercial, principio_ativo,
    forma_farmaceutica, concentracao, apresentacao, unidade_medida, preco_custo, preco_venda,
    estoque_minimo, estoque_maximo, necessita_receita, controlado, refrigerado
)
SELECT c.id, f.id, 'MED-DEMO-001', '7890000000001', 'Analgésico Demo', 'Substância fictícia A',
       'Comprimido', '500 mg', 'Caixa com 20', 'CX', 8.50, 14.90, 10, 100, FALSE, FALSE, FALSE
FROM categorias c CROSS JOIN fabricantes f
WHERE c.nome = 'Medicamentos' AND f.nome = 'Laboratório Exemplo'
ON CONFLICT (codigo_interno) DO NOTHING
SQL);
        $this->execute(<<<'SQL'
INSERT INTO medicamentos (
    categoria_id, fabricante_id, codigo_interno, codigo_barras, nome_comercial, principio_ativo,
    forma_farmaceutica, concentracao, apresentacao, unidade_medida, preco_custo, preco_venda,
    estoque_minimo, estoque_maximo, necessita_receita, tipo_receita, controlado, refrigerado
)
SELECT c.id, f.id, 'MED-DEMO-002', '7890000000002', 'Controlado Demo', 'Substância fictícia B',
       'Cápsula', '10 mg', 'Caixa com 30', 'CX', 22.00, 39.90, 8, 50, TRUE, 'Branca', TRUE, FALSE
FROM categorias c CROSS JOIN fabricantes f
WHERE c.nome = 'Medicamentos' AND f.nome = 'Laboratório Exemplo'
ON CONFLICT (codigo_interno) DO NOTHING
SQL);
        $this->execute(<<<'SQL'
INSERT INTO lotes (
    medicamento_id, numero_lote, data_fabricacao, data_validade, quantidade_recebida,
    quantidade_disponivel, preco_custo, data_recebimento, usuario_id
)
SELECT m.id, 'DEMO-FEFO-01', CURRENT_DATE - 30, CURRENT_DATE + 45, 25, 7, m.preco_custo,
       CURRENT_TIMESTAMP, u.id
FROM medicamentos m CROSS JOIN usuarios u
WHERE m.codigo_interno = 'MED-DEMO-001' AND u.email = 'admin@farmaclinic.local'
ON CONFLICT (medicamento_id, numero_lote) DO NOTHING
SQL);
        $this->execute(<<<'SQL'
INSERT INTO lotes (
    medicamento_id, numero_lote, data_fabricacao, data_validade, quantidade_recebida,
    quantidade_disponivel, preco_custo, data_recebimento, usuario_id
)
SELECT m.id, 'DEMO-FEFO-02', CURRENT_DATE - 15, CURRENT_DATE + 180, 40, 40, m.preco_custo,
       CURRENT_TIMESTAMP, u.id
FROM medicamentos m CROSS JOIN usuarios u
WHERE m.codigo_interno = 'MED-DEMO-002' AND u.email = 'admin@farmaclinic.local'
ON CONFLICT (medicamento_id, numero_lote) DO NOTHING
SQL);
    }
}
