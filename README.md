# Fintech MVP

## Sobre o projeto

O principal UseCase do projeto é a transferência entre dois usuários, no caso serão um usuário comun e um logista, antes de finalizar a transfêrencia, deve ser consultado um serviço externo e verificar se podemos aprovar a transferência.

Caso a transferência seja aprovada, é feito um envio de notificação por email e por sms.

Em caso de erro na transfêrencia, deve ser feito um estorno para conta do usuário pagador e a dedução do valor na conta do recebedor.

## A Minha interpretação do requisitos

Ao ler a descrição deu a entender que a intenção é mostrar uma solução do desenvolvedor sem depender totalmente do fremework.
Dito isso optei pelo uso do DDD, utilizando muito do Objects calistenics, linguagem ubiqua, utilizei os Value Objects para ajudar com a obsessão por tipos primitivos e tratamento de requests fora do framework.
Meu objetivo é desacoplar o máximo possível e entregar uma solução agnostiva ao framework.
Uma das parte que acabei utilizando do Framework foi o ORM, mas continuo estudando pra conseguir não depender dele no futuro.
Para a chamada dos provedores de atualização e notificação, entendi que poderia ser feito umas tentativas a mais antes de de fato cancelar então o job tem 3 tentativas antes da falha.

## Regras de negocio da implementação

 - Um usuário precisa ter Nome Completo, CPF/CNPJ (unico), EMAIL (unico), Telefone, Senha (Min 6 Caracteres).
 - Após o utilizador ser criado ele deve chamar a rota de criação da carteira(Create/Wallet) passando o tipo de carteira desejada.
 - Atualmente é criada uma carteira fake mas no mundo real acredito que nessa rota também seriam enviados documentos e uma requisição para um provedor bancário.
 - O que difere um lojista de um utilizador comum é a sua carteira, já que hoje é possivel fazer operações como lojista apenas com o CPF.
 - Não há rota de saldo mas basta ir até a tabela coluna balance da tabela wallets para adicionar.
 - A principal regra da transferência é que o Logista só pode receber valores, e o usuário comum recebe e envia.
 - Para fazer a transference o usuário precisa apenas passar o id do usuário que irá receber, pois é necessário que o pagador esteja logado, pois com o usuário logado, já possuimos as suas informações
 - Caso a transferência não tenha sucesso o mesmo job que faz a chamada do serviço de autorização, vai devolver os valores para o pagador e para o recebedor e atualizar a transação como type='refund'

## Configuração do Projeto

Dentro da pasta do projeto temos duas opções, configurar o sail, ou utilizar o docker compose.

## Configuração Sail

```bash
docker run --rm --interactive --tty \
  --volume $PWD:/app \
  --user $(id -u):$(id -g) \
  composer install
```

Para subir o ail

```bash
./vendor/bin/sail up -d
```
Configurações iniciais

```bash
./vendor/bin/sail up -d artisan composer install

./vendor/bin/sail up -d artisan key:generate

./vendor/bin/sail up -d artisan migrate:fresh --seed
```

## Configuração Docker Compose

Para subir o Docker

```bash
sudo docker compose up
```

Configurações iniciais

Execute os comandos abaixo dentro do contâiner **mvp-fintech-laravel.test-1**

```bash
sudo docker exec -it mvp-fintech-laravel.test-1 bash
```

```bash
php artisan composer install

php artisan key:generate

php artisan migrate:fresh --seed
```
#### Usuários do seeder
Type : Common
Balance : 100000
```bash
{
	"email": "andreew@gmail.com",
	"password": "123456"
}
```
Type : shop_kepeer
Balance : 0
```bash
{
	"email": "alecssander@gmail.com",
	"password": "123456"
}
```

## Endpois do projeto

Criei uma pasta na raiz do projeto com o nome de 'Dev' onde está o arquivo de exportação do insominia, mas também deixarei as rotas abaixo.

### Criação de usuário
Url: http://localhost/api/user/create
Request body
```bash
{
	"full_name": "Andreew Kennedy",
	"email": "ak@gmail.com",
	"cpf": "01822850657",
	"phone": "31993920011",
	"password": "123456"
}
```
Curl

```bash
curl --request POST \
  --url http://localhost/api/user/create \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
	"full_name": "Andreew Kennedy",
	"email": "ak@gmail.com",
	"cpf": "01822850657",
	"phone": "31993920011",
	"password": "123456"
}'
```
### Login
Url: http://localhost/api/user/login
Request body
```bash
{
	"email": "ak@gmail.com",
	"password": "123456"
}
```

```bash
curl --request POST \
  --url http://localhost/api/user/login \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header 'User-Agent: insomnia/11.0.1' \
  --data '{
	"email": "ak@gmail.com",
	"password": "123456"
}'
```

### Criação da Carteira
Url: http://localhost/api/wallet/create
Request body
Os tipos de carteira são: common e shop_keeper
*Necessário estar logado
```bash
{
	"type": "common"
}
```

```bash
curl --request POST \
  --url http://localhost/api/wallet/create \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer 5|K06KKyxjWxNS8VuJqLGuLKqjGf7IMfgRv6TpFJ12efa38699' \
  --header 'Content-Type: application/json' \
  --header 'User-Agent: insomnia/11.0.1' \
  --data '{
	"type": "common"
}'
```
### Transferência
Url: http://localhost/api/transfer/create
Request body
*Necessário estar logado
```bash
{
	"value": 5000,
	"payee_id": "01jr6vqyf825nx0hyxpg037hpt"
}
```

```bash
curl --request POST \
  --url http://localhost/api/transfer/create \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer 5|K06KKyxjWxNS8VuJqLGuLKqjGf7IMfgRv6TpFJ12efa38699' \
  --header 'Content-Type: application/json' \
  --header 'User-Agent: insomnia/11.0.1' \
  --data '{
	"value": 5000,
	"payee_id": "01jr6vqyf825nx0hyxpg037hpt"
}'
```

## Testes

Os testes têm uma automação para rodar assim que um commit for feito na branch main, mas também podem ser rodados manualmente.
Para executar os testes basta rodar o comando abaixo
No docker
```bash
php artisan test
```
No sail
```bash
./vendor/bin/sail artisan test
```

## Filas

Para executar as filas basta rodar o comando abaixo

No docker
```bash
php artisan queue:work
```
No sail
```bash
./vendor/bin/sail queue:work
```
