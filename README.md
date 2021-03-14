# YouPay

YouPay é apenas um nome simbólico de um negócio de pagamento via carteira digital. Neste pequeno projeto o negócio o principal é transferencia entre carteiras digitais. Os usuários precisam criar uma conta na plataforma na _YouPay_.

## Objetivo

O objetivo desse projeto é aplicar metodologias e boas práticas de codificação. Como exemplo, pode-se citar: SOLID, DDD, TDD e principalmente códigos testáveis (Testes de Unidade e testes de Integração). O principal objetivo é separar o _core_ da aplicação para não dependa de framework do momento, por isso foi escolhido o DDD (Domain-Driven Design) para que se em um futuro queira mudar de framework ou de banco de dados a aplicação não fique amarrada a um banco de dados ou um framework.

## Requisitos

-   [PHP 7.3+](https://www.php.net/)
-   [Composer](https://getcomposer.org/)
-   [Extensão de PDO](https://www.php.net/manual/en/pdo.installation.php) para o banco escolhido
-   Extensão do Sqlite

## Setup

-   Clone do projeto `git clone https://github.com/silasstoffel/YouPay.git`
-   Instalar depêndencias: `composer install`
-   Copiar o `.env.example` e renomear para `.env`
-   Parametrizar o `.env`. Como é uma aplicação que usa [Lumen](https://lumen.laravel.com/) grande parte das atributos contidos no arquivo são do framework. Nesse projeto o banco usado é o sqlite, mas nada impede de trocar um para um banco de dados que [Lumen](https://lumen.laravel.com/) suporta.
-   Se for usar o SQLite, no `.env` remova ou comente esses atribuitos: DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME e DB_PASSWORD. Paa comentar use um # no inicio de cada linha. Mantenha apenas DB_CONNECTION=sqlite na seção de banco de dados.
-   Se for usar o SQLite, copie/cole e renomei a cópia de `database/database.sqlite.example` para `database/database.sqlite`.
-   Rodar migrations para criar a base de dados: `php artisan migrate`.
-   Rodar seeders: `php artisan db:seed`. Isso criar uma conta inicial com um saldo R$ 500,00.
-   Levantar um servidor para rodar o projeto: `php -S localhost:8000 -t public`

Para começar a usar o projeto será criado duas contas inicialmente com os seguintes dados:

Conta Lojista

```json
{
    "id": "8b04b926-2d92-4977-ab57-82a6f03ba39c",
    "cpfcnpj": "97114152000157",
    "titular": "Conta Lojista",
    "email": "conta.lojista@youpay.com.br",
    "senha": "conta.master",
    "celular": "27988887777",
    "saldo": 0
}
```

Conta Comum

```json
{
    "id": "f4ca258a-3e68-4a83-984d-02a7c8bab5c7",
    "cpfcnpj": "71961965038",
    "titular": "Conta Comum",
    "email": "conta.comum@youpay.com.br",
    "senha": "conta.comum",
    "celular": "27988887654",
    "saldo": 500.0
}
```

Agora com às contas exemplos, é possível fazer transferência e também é possível criar novas contas, para isso, consulte a documentação da api feita usando [OpenAPI Specification - swagger](https://swagger.io/specification/). Para acessar a documentação, na sua propria instalação acesse o endereço: `http://localhost:8000/api-docs/index.html`.


## Regra de Negócio e Premissas

O negócio principal da youpay é bem simplificado limitando-se APENAS em transferência de recursos entre conta. Para isso é necessário que tenha um cadastro de contas, autenticação e transferencia de recursos.

- Existem dois grupos/perfils de contas, sendo conta comum e conta do lojista.
- Todo cadastro com CPF é considerado automaticamente conta comum.
- Todo cadastro com CNPJ é considerado automaticamente conta do lojista.
- Não pode haver mais de uma conta com CPF/CNPJ ou e-mail.
- Apenas usuários comuns podem transferir dinheiro, contas do perfil de logista não transferem dinheiro por este serviço, apenas recebem.
- Contas comuns podem enviar e receber dinheiro.


## Testes

Este projeto usa os recursos do framework [Lumen](https://lumen.laravel.com/) para rodar testes, o [Lumen](https://lumen.laravel.com/) por sua vez usa [PHPUnit](https://phpunit.de/) como framework de testes. O projeto tem cobertua de testes unitário e testes de integração (api).

Para rodar os testes execute pelo menos um comando das alternativas abaixo:

Unix:

`./vendor/bin/phpunit` ou `composer run tests`

Windows:

`.\vendor\bin\phpunit` ou `composer run tests-windows`
