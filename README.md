# FarmaClinic Manager

Sistema empresarial para gestão integrada de farmácia e atendimento clínico, desenvolvido com PHP 8.2+, CakePHP 5 e PostgreSQL.

## Estado do desenvolvimento

Etapa 1 — ambiente e instalação concluída. O domínio, as migrations e os módulos funcionais serão implementados nas próximas etapas do projeto.

## Stack

- PHP 8.2.31
- CakePHP 5.4.1
- PostgreSQL 15.3
- Composer 2.10.2
- PHPUnit 11.5

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

Resultado da Etapa 1: 9 testes e 23 assertions aprovados.

## Configuração

- `config/app.php`: driver PostgreSQL e configurações compartilhadas.
- `config/app_local.php`: configuração local gerada pelo CakePHP e ignorada pelo Git.
- `config/.env.example`: modelo seguro das variáveis de ambiente.
- `config/.env`: credenciais locais, ignoradas pelo Git.

O ambiente local usa locale `pt_BR`, fuso `America/Bahia` e UTF-8. Credenciais e salts nunca devem ser adicionados ao repositório.

## Próximas etapas

1. Migrations, seeds, fixtures e relacionamentos.
2. Autenticação, autorização, perfis e permissões.
3. Medicamentos, lotes e estoque.
4. Compras, vendas, clínica, financeiro, dashboard e API REST.
5. Auditoria, segurança e revisão LGPD.
