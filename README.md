# YouPay

YouPay é apenas um nome simbólico de um negócio de pagamento via carteira digital. Neste pequeno projeto o negócio o principal é transferencia entre carteiras digitais. Os usuários precisam criar uma conta na plataforma na _YouPay_.

## Objetivo

O objetivo desse projeto é aplicar metodologias e boas práticas de codificação. Como exemplo, pode-se citar: SOLID, DDD, TDD e principalmente códigos testáveis (Testes de Unidade e testes de Integração). O principal objetivo é separar o _core_ da aplicação para não dependa de framework do momento, por isso foi escolhido o DDD (Domain-Driven Design) para que se em um futuro queira mudar de framework ou de banco de dados a aplicação não fique amarrada a um banco de dados ou um framework.

## Requisitos

-   PHP 7.3+
-   Composer
-   Extensão de PDO habilitada
-   Extensão do Sqlite habilitada

## Setup

-   Clone do projeto `git clone https://github.com/silasstoffel/YouPay.git`
-   Instalar depêndencias: `composer install`
-   Copiar o `.env.example` e renomear para `.env`
-   Parametrizar o `.env`. Por se tratar de uma aplicação que usar Lumem grande parte das atributos são do framework, nesse projeto inicialmente usa o sqlite, mas nada impede de trocar um para um banco de dados que Lumem suporta.
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

Agora com às contas exemplos, é possível fazer transferência e também é possível criar novas, para isso consulta a documentação da api pelo endereço `http://localhost:8000/api-docs/index.html`.
