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
    }
}
