# KuantaGrana

KuantaGrana e uma aplicacao web de educacao e organizacao financeira com uma camada de gamificacao.
Em vez de tratar o sistema como um conjunto solto de modulos, a experiencia e organizada por ambientes, cada um representando um contexto da rotina do usuario.

## Visao geral

O fluxo principal do sistema funciona assim:

1. O usuario entra no mapa.
2. Escolhe o ambiente certo.
3. Registra o que faz sentido naquele contexto.
4. Evolui com pontos, niveis, conquistas e desafios.

Hoje, a estrutura do produto esta organizada da seguinte forma:

- `Casa`: receitas, despesas da base da rotina e itens da casa
- `Mercado`: compras e reposicao de mantimentos
- `Farmacia`: gastos e itens ligados a saude e cuidado
- `Escola`: dicas e educacao financeira
- `Parque de Diversoes`: metas, progresso e recompensas

## Principais funcionalidades

- mapa de ambientes como entrada principal da experiencia
- dashboard com saldo do mes, metas, alertas de itens, nivel e progresso
- transacoes financeiras guiadas por ambiente
- itens guiados por ambiente
- metas concentradas no Parque de Diversoes
- sistema de pontos, niveis, conquistas e desafios
- dicas educativas associadas aos ambientes

## Arquitetura

O projeto segue uma organizacao em camadas bem definida:

- `Controllers`: entrada HTTP e orquestracao do fluxo
- `Services`: regras de negocio e agregacao de dados
- `Models`: entidades do dominio e relacionamentos
- `Views`: interface em Blade com Tailwind
- `Seeders`: dados iniciais do mundo do sistema

Um ponto importante da arquitetura atual e a centralizacao das capacidades dos ambientes em:

- `app/Support/EnvironmentCatalog.php`

Esse catalogo define:

- quais ambientes aceitam transacoes
- quais ambientes aceitam itens
- qual ambiente aceita metas
- highlights e tema visual de cada ambiente

## Stack utilizada

- PHP `8.3`
- Laravel `13`
- Laravel Breeze
- Blade
- Tailwind CSS
- Vite
- Alpine.js
- Pest

## Como rodar o projeto

### 1. Instalar dependencias

```bash
composer install
npm install
```

### 2. Configurar ambiente

```bash
copy .env.example .env
php artisan key:generate
```

Configure as variaveis do `.env`, principalmente banco de dados.

### 3. Rodar migrations e seeders

```bash
php artisan migrate
php artisan db:seed
```

### 4. Iniciar a aplicacao

Voce pode usar o fluxo padrao do Composer:

```bash
composer run dev
```

Ou subir separadamente:

```bash
php artisan serve
npm run dev
```

## Scripts uteis

```bash
composer run dev
composer run test
npm run dev
npm run build
```

## Testes

Para executar a suite automatizada:

```bash
php artisan test
```

No estado atual do projeto, a suite de testes de feature e unidade esta passando.

## Estrutura importante do projeto

```text
app/
  Http/Controllers/
  Http/Requests/
  Models/
  Services/
  Support/
database/
  migrations/
  seeders/
docs/
  system-map.md
resources/
  views/
  css/
  js/
routes/
  web.php
tests/
  Feature/
```

## Documentacao interna

O projeto possui um mapa tecnico mais detalhado em:

- [docs/system-map.md](docs/system-map.md)

Esse arquivo descreve:

- os ambientes
- os fluxos principais
- as capacidades por contexto
- a navegacao principal
- os relacionamentos do dominio

## Direcao atual do produto

O sistema foi evoluido para ficar mais coeso em tres frentes:

- `Mapa` como entrada principal
- modulos globais como atalhos, e nao como centro da experiencia
- fluxos de transacoes, itens e metas ancorados em ambiente

Em resumo, o KuantaGrana hoje e menos um CRUD financeiro tradicional e mais uma experiencia contextual de organizacao financeira com progressao gamificada.
