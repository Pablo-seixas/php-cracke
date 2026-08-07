# FarmaClinic Manager

Sistema empresarial para gestão integrada de farmácia e atendimento clínico, desenvolvido com PHP 8.2+, CakePHP 5 e PostgreSQL.

## Estado do desenvolvimento

Etapas concluídas:

- Etapa 1 — ambiente, instalação e PostgreSQL.
- Etapa 2 — schema, migrations, seed, fixtures e relacionamentos ORM.
- Etapa 3 — autenticação, autorização, usuários, perfis, permissões e bloqueio de login.

Os módulos farmacêuticos e clínicos serão implementados a partir da Etapa 4.

## Stack

- PHP 8.2.31
- CakePHP 5.4.1
- PostgreSQL 15.3
- Composer 2.10.2
- PHPUnit 11.5
- CakePHP Authentication 3.3
- CakePHP Authorization 3.5
- Bootstrap 5.3

## Requisitos

- PHP 8.2 ou superior
- Extensões PHP `intl`, `pdo_pgsql`, `pgsql` e `zip`
- PostgreSQL 15 ou superior
- Composer 2
- Git

## Instalação

```powershell
composer install
Copy-Item config/.env.example config/.env
```

Edite `config/.env` e substitua `CHANGE_ME` por uma senha local segura. O arquivo é ignorado pelo Git e não deve ser versionado.

Crie o usuário e os bancos no PostgreSQL:

```sql
CREATE ROLE farmaclinic LOGIN PASSWORD 'SENHA_LOCAL_SEGURA';
CREATE DATABASE farmaclinic OWNER farmaclinic ENCODING 'UTF8';
CREATE DATABASE farmaclinic_test OWNER farmaclinic ENCODING 'UTF8';
```

Execute as migrations e os dados iniciais:

```powershell
php bin/cake.php migrations migrate
php bin/cake.php migrations migrate --connection test
php bin/cake.php seeds run InitialData
```

Valide a conexão e execute o servidor:

```powershell
php bin/cake.php migrations status
php bin/cake.php server -H localhost -p 8765
```

A aplicação estará disponível em `http://localhost:8765`.

## Testes

```powershell
php vendor/bin/phpunit
```

Resultado da Etapa 3: 17 testes e 89 assertions aprovados.

## Dados de demonstração

- Usuário: `admin@farmaclinic.local`
- Senha: `Demo@12345`

Esses dados são fictícios e destinados somente ao desenvolvimento.

O login está disponível em `http://localhost:8765/login`.

## Autenticação e autorização

- Sessão e formulário processados pelo CakePHP Authentication.
- Policies e resolução ORM fornecidas pelo CakePHP Authorization.
- Senhas armazenadas exclusivamente com hash seguro.
- Usuários inativos, excluídos ou temporariamente bloqueados não são autenticados.
- Cinco falhas em quinze minutos bloqueiam a conta por trinta minutos.
- Login, logout e mudanças de permissões são auditados.
- Cookies usam `HttpOnly`, `SameSite=Lax` e modo estrito.
- Defina `SESSION_COOKIE_SECURE=true` em produção HTTPS.
- Logout aceita somente requisições POST protegidas por CSRF e FormProtection.

## Banco de dados

A migration inicial cria 41 tabelas de domínio. Os principais relacionamentos são:

```text
usuarios --< usuarios_perfis >-- perfis --< perfis_permissoes >-- permissoes
categorias --< medicamentos >-- fabricantes
medicamentos --< lotes --< movimentacoes_estoque
fornecedores --< cotacoes --< cotacoes_itens
solicitacoes_compras --< solicitacoes_compras_itens
compras --< compras_itens
clientes --< vendas --< vendas_itens
pacientes --< consultas --< atendimentos
pacientes -- prontuarios --< prontuarios_historicos
vendas --< pagamentos >-- caixas --< movimentacoes_caixa
```

Valores monetários usam `NUMERIC`, validade usa `DATE`, eventos usam `TIMESTAMPTZ`, auditoria usa `JSONB` e endereços de rede usam `INET`.

## Configuração

- `config/app.php`: driver PostgreSQL e configurações compartilhadas.
- `config/app_local.php`: configuração local gerada pelo CakePHP e ignorada pelo Git.
- `config/.env.example`: modelo seguro das variáveis de ambiente.
- `config/.env`: credenciais locais, ignoradas pelo Git.

O ambiente local usa locale `pt_BR`, fuso `America/Bahia` e UTF-8. Credenciais e salts nunca devem ser adicionados ao repositório.

## Próximas etapas

1. Medicamentos, produtos, categorias, fabricantes, lotes e validade.
2. Estoque e alertas.
3. Compras, vendas, clínica, financeiro, dashboard e API REST.
4. Auditoria, segurança e revisão LGPD.
