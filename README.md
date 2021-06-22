# Promobit - Test
Projeto proposto ao Jhonatha Matias, consiste em criar uma API Stateless com as seguintes funcionalidades:
- CRUD de usuários
- Login
- Recuperação de senha

## Requisito do projeto
- Docker + docker-compose
- Ambiente Linux

## Tecnologias utilizadas
- PHP 8
- MYSQL 8
- Symfony 5.2

#### Clone do projeto
```
https://github.com/jhonathamatias/promobit-teste.git
```

#### Instalação
Abra o terminal, acesse a pasta do projeto e execute:
```
./boot
```

### Banco de dados
Para criar o banco, basta executar esse comando no seu terminal:
```
./symfony doctrine:database:create
```
Para criar as tabelas, execute a migration:
```
./symfony doctrine:migration:migrate
```
### Configuração Mailjet
No .env da sua aplicação é necessário alterar as informações abaixo.
**OBS: Mas para fins de teste, minhas configurações vão estar no .env por um tempo**
```
MJ_APIKEY_PRIVATE=sua_apikey_private
MJ_APIKEY_PUBLIC=sua_apikey_public
MJ_FROM_EMAIL=seuemail@gmail.com
MJ_FROM_NAME=um_nome
```
### Pronto a API esta pronta para ser consumida
- Base url da API: http://localhost:8888


# Exemplos:


### Sign up 
**Endpoint:** `POST - /signup` para criar um usúario:

    curl -d '{"name":"seunome", "email":"seuemail", "password":"123456"}' -H "Content-Type: application/json" -X POST http://localhost:8888/signup

### Sign in
Ao fazer login você receberá um token para conseguir consumir a API

**Endpoint:** `POST - /signin` para fazer login:

    curl -d '{"email":"seuemail", "password":"123456"}' -H "Content-Type: application/json" -X POST http://localhost:8888/signin

### Listar usúarios
Utilize o token recebido no login para listar os usúarios

**Endpoint:** `GET - /users` listar usúarios:

    curl -d  -H "Content-Type: application/json" -H "Authorization: Bearer token" -X GET http://localhost:8888/users

### Alterar um usúario
Utilize o token recebido no login para atualizar um usúario

**Endpoint:** `PUT - /users/{id}` alterar usúario:

    curl -d '{"name":"josefa"}' -H "Content-Type: application/json" -H "Authorization: Bearer token" -X PUT http://localhost:8888/users/1
    
### Deletar um usúario
Utilize o token recebido no login para deletar um usúario

**Endpoint:** `DELETE - /users/{id}` deletar usúario:

    curl -d -H "Content-Type: application/json" -H "Authorization: Bearer token" -X DELETE http://localhost:8888/users/1
    
### Recuperação de senha
Ao fazer a chamada na rota de recuperação de senha, o usuário receberá um email com um token para ser utilizado na rota de alteração de senha. É necessário informar o email de um usúario cadastrado
**OBS: Verifique a caixa de SPAM caso não receba na sua caixa de entrada**

**Endpoint:** `POST - /forget` recuperação de senha:

    curl -d '{"email":"emaildousuario"}' -H "Content-Type: application/json" -X POST http://localhost:8888/forget
### Alteração de senha
Utilize o token recebido no email e informe uma nova senha

**Endpoint:** `PUT - /users/forget/password` alterar senha do usúario:


    curl -d '{"password":"123456"}' -H "Content-Type: application/json" -H "Authorization: Bearer token" -X PUT http://localhost:8888/users/1