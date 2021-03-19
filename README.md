# YouPay

YouPay é apenas um nome simbólico de um negócio de pagamento via carteira digital. Neste pequeno projeto o negócio principal é transferencia entre carteiras digitais. Os usuários precisam criar uma conta na plataforma na _YouPay_.

## Objetivo

O objetivo desse projeto é aplicar metodologias e boas práticas de codificação. Como exemplo, pode-se citar: SOLID, DDD, TDD, Repository e principalmente códigos testáveis (Testes de Unidade e testes de Integração). O principal objetivo se tratando do ponto de codificação é separar o _core_ da aplicação para não fique acomplado ao um framework, por isso foi escolhido o DDD (Domain-Driven Design) para que se em um futuro queira mudar de framework ou de banco de dados a aplicação não fique amarrada a um banco de dados ou um framework.

A aplicação usa [Lumen](https://lumen.laravel.com/) como framework e para camada de infra no caso banco de dados, está sendo [Eloquent ORM](https://laravel.com/docs/8.x/eloquent). Porém independente detalhes  e aspectos técnicos, o core da aplicação fica flexível a mudanças de frameworks e banco de dados.


## Requisitos

-   [PHP 7.4](https://www.php.net/)
-   [Composer](https://getcomposer.org/)
-   [Extensão de PDO MySQL e SQLite](https://www.php.net/manual/en/pdo.installation.php) para o banco escolhido

## Setup

### Com Docker

Para facilitar o ambiente de execução do projeto, pode ser levantado o ambiente com docker compose, siga os passos:

-   Copiar o `.env.example`, renomear a cópia para `.env` e parametrizar conforme necessidade.
-   Rodar o comando `docker-compose up -d --build`.
-   Acessar o container `docker exec -it ${nome-do-servico} bash`.
-   Navegar até `cd /var/www`.
-   Instalar depêndencias: `composer install`.
-   Rodar migrations: `php artisan migrate`.
-   Rodar seeders: `php artisan db:seed`. O comando cria uma conta inicial com um saldo R$ 500,00.
-   Acessar `http://localhost:8080`

### Setup Manual

-   Instalar depêndencias: `composer install`.
-   Copiar o `.env.example` e renomear para `.env`.
-   Copiar o `.env.example`, renomear a cópia para `.env` e parametrizar conforme necessidade.
-   Rodar migrations: `php artisan migrate`.
-   Rodar seeders: `php artisan db:seed`. O comando cria uma conta inicial com um saldo R$ 500,00.s
-   Levantar um servidor para rodar o projeto: `php -S localhost:8080 -t public`

### Considerações do Setup

Para começar a usar o projeto serão criadas duas contas inicialmente, para facilitar o setup e já existir contas para transferência. As contas criadas possuem com os seguintes dados:

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

Agora com às contas exemplos, é possível fazer transferência e também é possível criar novas contas, para isso, consulte a documentação da api feita usando [OpenAPI Specification - swagger](https://swagger.io/specification/). Para acessar a documentação, na sua própria instalação acesse o endereço: `http://localhost:8080/api-docs/index.html`.


## Regra de Negócio e Premissas

O negócio principal da _youpay_ é bem simplificado limitando-se APENAS em transferência de recursos entre conta. Para isso é necessário que tenha um cadastro de contas, autenticação e transferencia de recursos.

- Existem dois grupos/perfils de contas, sendo conta comum e conta do lojista.
- Todo cadastro com CPF é considerado automaticamente conta comum.
- Todo cadastro com CNPJ é considerado automaticamente conta do lojista.
- Não pode haver mais de uma conta com CPF/CNPJ ou e-mail.
- Apenas usuários comuns podem transferir dinheiro, contas do perfil de logista não transferem dinheiro por este serviço, apenas recebem.
- Contas comuns podem enviar e receber dinheiro.


## Testes

Este projeto usa os recursos do framework [Lumen](https://lumen.laravel.com/) para rodar testes, o [Lumen](https://lumen.laravel.com/) por sua vez usa [PHPUnit](https://phpunit.de/) como framework de testes. O projeto tem cobertua de testes unitário e testes de integração (api).

Os testes de integração que usam banco de dados precisa da extensão PDO SQLite, então certique que atenda os requisitos.

Para rodar os testes execute pelo menos um comando das alternativas abaixo:

Unix:

`./vendor/bin/phpunit` ou `composer run tests`

Windows:

`.\vendor\bin\phpunit` ou `composer run tests-windows`

Para efetivar testes de integração o banco de dado utilizado é banco SQLite em memória. A configuração dos testes está parametrizada no arquivo `.env.testing`.

## Extras

Para testar a API de forma visual, pode ser feito tanto pelo swagger `http://localhost:8080/api-docs/index.html` ou pelo [insomnia](https://insomnia.rest/products/insomnia). Caso faça pelo [insomnia](https://insomnia.rest/products/insomnia), no projeto já existe um [arquivo](./Endpoints-Insomnia.json) base que pode ser importado na sua instalação.

Apesar de haver ambas opções de teste visual da API, o teste pode ser feito com qualquer client rest.
d
