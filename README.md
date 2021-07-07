# api-rede-social

API para Rede Social desenvolvida em Laravel 7 e banco de dados MySQL

### Pré-requisitos globais:
- [x]  O PHP o caminho para o binário do PHP deve estar no PATH do sistema operacional
- [x]  O Composer deve estar instalado de forma global no sistema
#### Biblioteca para autenticação:
`composer require tymon/jwt-auth`
- Para aprender a configurar o **tymon/jwt-auth**, baste seguir este [tutorial](https://jwt-auth.readthedocs.io/en/develop/laravel-installation/)
#### Biblioteca para manipulação de imagens:
`composer require intervention/image`
- Para aprender a configurar o **intervention/image**, baste seguir este [tutorial](http://image.intervention.io/getting_started/installation#laravel)

### Instalação
- Faça o clone deste repositório para a sua máquina
- Rode o comando: `composer install`
- Depois esse: `php artisan key:generate`

### Para rodar o projeto
- Digite o comando: `php artisan serve`
