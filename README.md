# Método de autenticação entre aplicações Laravel

[![Latest Stable Version](http://poser.pugx.org/hafael/laravel-mesh-auth/v)](https://packagist.org/packages/hafael/laravel-mesh-auth)
[![Latest Unstable Version](http://poser.pugx.org/hafael/laravel-mesh-auth/v/unstable)](https://packagist.org/packages/hafael/laravel-mesh-auth)
[![Total Downloads](http://poser.pugx.org/hafael/laravel-mesh-auth/downloads)](https://packagist.org/packages/hafael/laravel-mesh-auth)
[![License](http://poser.pugx.org/hafael/laravel-mesh-auth/license)](https://packagist.org/packages/hafael/laravel-mesh-auth)

Este pacote permite a comunicação onde: o microserviço "A" (lado cliente) gera uma chave de API no microserviço "B" (lado servidor) através de um método duplo de autenticação baseada em tokens sanctum.

Uma rota protegida no lado servidor por um par de chaves é capaz de gerar um token sanctum em nome de um usuário registrado e devolvida através de uma resposta http no formato json.
O cliente (A) deve armazenar a chave para ser utilizada nas rotas protegidas pelo middleware no servidor (B).

Se o pacote for instalado nos dois (ou mais) lados, permite uma comunicação bi-direcional entre as aplicações. 

Redes Mesh também são conhecidas como redes de tráfego East-West (Leste-Oeste), e em poucas palavras pode ser definida como uma "camada" de rede abstrata para comunicações de serviços.

Exemplo de estrutura:

- Clientes (A)
- Pedidos (B)
- Relatórios (C) 

Caso:
Um usuário administrador precisa de um relatório de vendas com um totalizador de pedidos realizados por cada cliente. Cada aplicação está separada por instalações Laravel distintas e banco de dados isolados em servidores diferentes.

Como funnciona:
Determinado usuário acessa a aplicação "C", que por sua vez precisa consultar a base de clientes no serviço "A", que por sua vez a aplicação "A" agrega na mesma consulta a quantidade de pedidos realizados na aplicação "B".

Um par de chave deve ser compartilhado entre todas as instalações: APP_SHARED_KEY e APP_SHARED_SECRET.

## 💡 Requirements

PHP 7.3 or higher
Laravel 8 or higher


## 📦 Installation 

1. Instale o pacote no lado do servidor:
`composer require "hafael/laravel-mesh-auth"`

2. Ainda no lado servidor, configure as chaves compartilhadas no respectivo arquiv .env

APP_SHARED_KEY=
APP_SHARED_SECRET=

3. Publique o arquivo de configuração:

`php artisan vendor:publish --tag=auth-mesh-config`

4. Migre o banco de dados:

`php artisan migrate --tag=auth-mesh-database`

5. Inclua a trait na classe do usuário

```php
  ...
  class User extends Authenticatable //implements MustVerifyEmail
  {
      use ...
          HasApiTokens,//required
          WithMeshAuth; //<---- package trait

      /**
       * The attributes that are mass assignable.
       *
       * @var array
       */
      protected $fillable = [
          'name', 
          'lastname', 
          'email',
      ....
```

6. Registre o middleware de autenticação em app/Http/Kernel.php

```php
  ...
  
  protected $routeMiddleware = [
      ...
      'auth.mesh' => \Hafael\Mesh\Auth\Middlewares\MeshTokenMiddleware::class,
  ];

```

7. Repita os passos anteriores no lado do cliente ou em todas aplicações que desejar.

Veja abaixo um exemplo sobre como emitir um token no servidor:


## 🌟 Getting Started
  
  No lado cliente, construa a seguinte requisição
  
```php
  <?php

    $user = Auth::user();
    $sharedSecret = env('APP_SHARED_SECRET');

    //Dessa forma é possível identificar o usuário solicitante no lado servidor.
    $apiSecret = base64_encode( $user->id . '|' . $sharedSecret);
    
    //Solicite um token sanctum
    $response = Http::withHeaders([
      'X-API-KEY' => env('APP_SHARED_KEY'),
      'X-API-SECRET' => $apiSecret,
    ])->acceptJson()
      ->post('http://server-side-app.com/api/auth/token', [
          'name' => 'ClientAppName',
          'abilities' => ['*'],
      ]);

    $tokenName = $response['name'];
    $accessToken = $response['access_token'];
    $tokenAbilities = $response['abilities'];
    
    //Armazene o token relacionado ao usuário autenticado:
    $user->savePlainTextToken($accessToken, $tokenName, $tokenAbilities);

    //Crie uma nova requisição incluindo o token de acesso recém gerado.
    $authResponse = Http::withHeaders([
      'Authorization' => 'Bearer '. $accessToken,
    ])->acceptJson()
      ->get('http://server-side-app.com/api/user');

    var_dump($authResponse);

    die;

  ?>
```

O mesmo procedimento pode ser gerado na direção servidor -> cliente.


## 📜 License 

MIT license. Copyright (c) 2023 - [Hafael](https://github.com/hafael)
For more information, see the [LICENSE](https://github.com/hafael/laravel-mesh-auth/blob/main/LICENSE) file.