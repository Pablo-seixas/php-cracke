FarmaClinic Manager
===================

Sistema web para reunir rotinas de farmácia e atendimento clínico em uma única aplicação. O projeto usa CakePHP 5, PostgreSQL e uma organização em camadas para manter regras de negócio fora dos controllers.

SOBRE O PROJETO
---------------

O FarmaClinic Manager foi criado como projeto de portfólio para representar fluxos que normalmente ficam separados em sistemas de estoque, venda e atendimento. O código atual cobre desde o cadastro de medicamentos e controle por lote até agenda, atendimento e prontuário versionado.

FUNCIONALIDADES CONCLUÍDAS
--------------------------

- Login, logout, recuperação de acesso e bloqueio por tentativas inválidas
- Administração de usuários, perfis e permissões
- Medicamentos, produtos, categorias e fabricantes
- Lotes, validade, estoque e movimentações sem exclusão física
- Fornecedores, solicitações, cotações, aprovação e recebimento de compras
- Vendas, pagamentos, cancelamento, caixa e estorno de estoque
- Pacientes, profissionais, especialidades, agenda e consultas
- Atendimentos e prescrições registradas
- Prontuário com autorização, auditoria de visualização e histórico de versões
- Dashboard gerencial com filtros e exportação de vendas em CSV
- Auditoria aplicada às operações críticas já implementadas

EM DESENVOLVIMENTO
------------------

- Ampliação dos indicadores e relatórios gerenciais
- Cobertura de contas a pagar e contas a receber na interface
- Revisão final de segurança e requisitos da LGPD

PLANEJADO
---------

- API REST versionada em api/v1
- Documentação dos endpoints da API
- Exportações adicionais e anexos clínicos

TECNOLOGIAS
-----------

- PHP 8.2 ou superior
- CakePHP 5
- PostgreSQL 15 ou superior
- CakePHP ORM e Migrations
- CakePHP Authentication e Authorization
- Composer 2
- Bootstrap 5
- JavaScript e Chart.js
- PHPUnit 11
- Git

ARQUITETURA
-----------

Os controllers recebem a requisição e coordenam a resposta. As regras de estoque, compra, venda, agenda, atendimento e prontuário ficam em services. Tables e entities representam o acesso e os dados do domínio. Policies aplicam as permissões, exceptions representam falhas esperadas e templates cuidam somente da apresentação. Consultas específicas de estoque ficam em repository.

BANCO DE DADOS
--------------

O projeto usa PostgreSQL. A migration inicial cria 41 tabelas, chaves estrangeiras, índices, restrições e campos de auditoria. Valores monetários usam NUMERIC, datas de validade usam DATE e eventos usam TIMESTAMPTZ. Dados estruturados, como horários e alterações auditadas, usam JSONB.

Relacionamentos principais:

    usuarios -- usuarios_perfis -- perfis -- perfis_permissoes -- permissoes
    categorias -- medicamentos -- lotes -- movimentacoes_estoque
    fornecedores -- compras -- compras_itens
    vendas -- vendas_itens -- pagamentos
    pacientes -- consultas -- atendimentos
    pacientes -- prontuarios -- prontuarios_historicos

SEGURANÇA
---------

- Senhas armazenadas com hash
- Autenticação e autorização integradas ao CakePHP
- Policies com permissões por perfil e privilégio administrativo explícito
- Proteção CSRF e Form Protection nos formulários
- Consultas parametrizadas pelo ORM e pela camada de banco do CakePHP
- Validação no servidor e escape de saída nos templates
- Bloqueio temporário após tentativas inválidas de login
- Cookies HttpOnly, SameSite e opção Secure para produção
- Auditoria de acessos e alterações em dados clínicos
- Credenciais locais mantidas fora do Git

INSTALAÇÃO
----------

Pré-requisitos: PHP 8.2 ou superior, PostgreSQL 15 ou superior, Composer 2 e Git.

Clone o repositório:

    git clone git@github.com:Pablo-seixas/php-cracke.git
    cd php-cracke

Instale as dependências:

    composer install

Crie a configuração local:

    Copy-Item config/.env.example config/.env

Edite config/.env e defina um salt próprio, a URL da aplicação e as URLs de conexão dos bancos local e de testes. Não versione esse arquivo.

Crie os bancos PostgreSQL e aplique a estrutura:

    php bin/cake.php migrations migrate
    php bin/cake.php migrations migrate --connection test
    php bin/cake.php seeds run InitialData

Inicie a aplicação:

    php bin/cake.php server -H 127.0.0.1 -p 8765

Acesse http://127.0.0.1:8765.

DADOS DE DEMONSTRAÇÃO
---------------------

Os dados iniciais são fictícios e destinados somente ao ambiente de desenvolvimento.

    E-mail: admin@farmaclinic.local
    Senha: Demo@12345

TESTES
------

Execute:

    php vendor/bin/phpunit

Na última validação local foram aprovados 45 testes e 150 assertions. O banco farmaclinic_test precisa estar configurado antes da execução.

ESTRUTURA DO PROJETO
--------------------

    config/
      Migrations/
      Seeds/
    src/
      Controller/
      Exception/
      Middleware/
      Model/
      Policy/
      Repository/
      Service/
    templates/
    tests/
      Fixture/
      TestCase/
    webroot/

STATUS DO PROJETO
-----------------

Backend e interface web funcionais para os módulos publicados. O projeto continua em desenvolvimento; a API REST e a revisão final de publicação ainda não fazem parte desta versão.

PRÓXIMAS ETAPAS
---------------

- Implementar e testar a API REST versionada
- Completar os fluxos financeiros restantes
- Ampliar relatórios e exportações
- Concluir a revisão de segurança, LGPD e documentação

AUTOR
-----

Pablo Seixas
