# Deploy no Oracle Cloud Free

Este guia foi pensado para o estado atual do KuantaGrana:

- Laravel `13`
- PHP `8.3`
- Vite
- SQLite por padrao
- sessoes, cache e fila em `database`

O objetivo aqui e subir o sistema de forma simples usando uma VM Always Free da Oracle Cloud na regiao de Sao Paulo.

## 1. Criar a conta e escolher a regiao correta

Ao criar a conta na Oracle Cloud, escolha a regiao home:

- `Brazil East (Sao Paulo)` / `sa-saopaulo-1`

Isso importa porque os recursos Always Free precisam ser criados na regiao home.

## 2. Criar a rede da forma mais simples

Antes da VM, vale criar a rede pelo assistente da Oracle.

No painel da Oracle Cloud:

1. Abra o menu lateral.
2. Va em `Networking` > `Virtual cloud networks`.
3. Escolha o compartimento em que voce vai trabalhar.
4. Clique em `Start VCN Wizard`.
5. Escolha `Create VCN with Internet Connectivity`.
6. Clique novamente em `Start VCN Wizard`.

Preencha assim:

- `VCN Name`: algo como `kuantagrana-vcn`
- `Compartment`: o mesmo compartimento em que a VM sera criada
- `Configure VCN`: mantenha os valores padrao
- `Configure public subnet`: mantenha os valores padrao
- `Configure private subnet`: mantenha os valores padrao

Depois:

1. Clique em `Next`
2. Revise
3. Clique em `Create`

Esse assistente cria a VCN, sub-rede publica, sub-rede privada, internet gateway e regras de roteamento basicas.

## 3. Criar a instancia na Console atual

No painel da Oracle Cloud:

1. Abra o menu lateral.
2. Va em `Compute` > `Instances`.
3. Escolha o compartimento correto.
4. Clique em `Create instance`.

A tela atual da Oracle costuma dividir a criacao em:

- `1. Basic information`
- `2. Security`
- `3. Networking`
- `4. Storage`
- `Review`

### 3.1 Basic information

Preencha assim:

- `Name`: `kuantagrana-prod` ou outro nome simples
- `Create in compartment`: seu compartimento

Em `Placement`:

- `Availability domain`: se aparecer a opcao relacionada a `Always Free Eligible`, prefira ela
- `Capacity type`: `On-demand capacity`
- `Fault domain`: deixe a Oracle escolher

Em `Image and shape`:

1. Clique em `Change image` se a imagem nao for Ubuntu.
2. Em `Platform images`, escolha `Ubuntu`.
3. Selecione uma versao atual estavel, como `Ubuntu 24.04` ou `Ubuntu 22.04`.
4. Confirme em `Select image`.

Depois:

1. Clique em `Change shape`
2. Em `Instance type`, escolha `Virtual machine`
3. Filtre por shape `Always Free`
4. Escolha uma destas opcoes:

- `VM.Standard.E2.1.Micro`: mais simples para comecar
- `VM.Standard.A1.Flex`: melhor em recursos, mas pode ter menos disponibilidade

Para o seu caso, eu recomendo comecar por:

- `VM.Standard.E2.1.Micro`

Se essa shape nao aparecer ou der erro de capacidade, tente:

- `VM.Standard.A1.Flex` com `1 OCPU` e `6 GB` de memoria

Observacoes importantes:

- os recursos Always Free precisam ser criados na `home region`
- se aparecer erro `out of host capacity`, isso normalmente significa falta temporaria de capacidade naquela shape ou AD
- nesse caso, tente outro availability domain ou tente novamente depois

### 3.2 Security

Pode manter o padrao:

- `Shielded instance`: desabilitado
- `Confidential computing`: desabilitado

### 3.3 Networking

Aqui e onde muita gente se confunde.

No bloco de rede:

- `Virtual cloud network`: selecione a VCN criada no passo anterior
- `Subnet`: selecione a sub-rede publica dessa VCN
- `Assign a public IPv4 address`: `Yes`
- `Use network security groups to control traffic`: pode deixar `No` por enquanto
- `DNS record`: pode deixar `Yes`

O ponto mais importante aqui e:

- a VM precisa estar em uma `public subnet`
- a VM precisa receber `public IPv4`

Sem isso, voce nao consegue acessar por SSH de forma simples.

### 3.4 Add SSH keys

Na secao de chaves SSH:

- se voce quer simplicidade, escolha `Generate a key pair for me`

Depois clique em:

- `Save Private Key`
- `Save Public Key`

Guarde especialmente a `private key`. Voce vai usar esse arquivo para conectar por SSH. Depois que a instancia for criada, a Oracle nao mostra essa chave privada de novo.

Se voce ja tiver sua propria chave SSH, pode usar a opcao de enviar a sua chave publica.

### 3.5 Storage

Pode manter o padrao.

Para o primeiro deploy:

- nao anexe block volume extra
- mantenha o boot volume padrao

### 3.6 Review e Create

Revise e clique em `Create`.

Depois disso:

1. espere o status sair de `Provisioning`
2. aguarde ficar `Running`
3. abra a pagina da instancia
4. copie o `Public IP address`

## 4. Liberar as portas da VM

Depois que a instancia for criada, a Oracle ainda pode bloquear o acesso por causa das regras da rede.

Para esse projeto, voce precisa liberar estas portas:

- `22` para acessar a VM por SSH
- `80` para abrir o site em HTTP
- `443` para abrir o site em HTTPS

### Jeito mais facil de achar a tela certa

1. Abra a pagina da sua instancia
2. Na area de detalhes da rede, clique no nome da `Subnet`
3. Na pagina da subnet, procure a secao `Security Lists`
4. Clique na security list da sub-rede publica
5. Abra `Ingress Rules` ou `Security Rules`
6. Clique em `Add Ingress Rules`

Se a Oracle mostrar nomes um pouco diferentes, a logica e esta:

- `Instance` -> `Subnet` -> `Security List` -> `Ingress Rules`

### O que voce vai adicionar

Crie tres regras de entrada.

#### Regra 1: SSH

- `Source Type`: `CIDR`
- `Source CIDR`: `0.0.0.0/0`
- `IP Protocol`: `TCP`
- `Destination Port Range`: `22`
- descricao opcional: `SSH`

Essa regra libera acesso remoto para voce entrar na maquina.

#### Regra 2: HTTP

- `Source Type`: `CIDR`
- `Source CIDR`: `0.0.0.0/0`
- `IP Protocol`: `TCP`
- `Destination Port Range`: `80`
- descricao opcional: `HTTP`

Essa regra permite abrir o site no navegador sem HTTPS.

#### Regra 3: HTTPS

- `Source Type`: `CIDR`
- `Source CIDR`: `0.0.0.0/0`
- `IP Protocol`: `TCP`
- `Destination Port Range`: `443`
- descricao opcional: `HTTPS`

Essa regra permite abrir o site com certificado SSL.

### O que significa `0.0.0.0/0`

Esse valor significa:

- permitir acesso a partir de qualquer IP da internet

Para `80` e `443`, isso e normal.

Para `22`, isso funciona, mas e menos seguro. Depois que tudo estiver funcionando, voce pode trocar:

- de `0.0.0.0/0`
- para `SEU_IP_PUBLICO/32`

Exemplo:

- se seu IP for `200.100.50.10`, a regra pode ficar `200.100.50.10/32`

Assim, so o seu computador consegue acessar a VM por SSH.

## 5. Acessar a maquina

### Se voce usa Windows

O jeito mais simples e usar o `PowerShell` ou o `Windows Terminal`.

1. Salve o arquivo da chave privada `.key` ou `.pem` em uma pasta facil de achar
2. Evite pastas sincronizadas pelo `OneDrive`, porque isso pode atrapalhar as permissoes da chave
3. Um caminho simples e:

```text
C:\ssh\oracle.key
```

4. Abra o `PowerShell`
5. Rode o comando abaixo, trocando o caminho da chave e o IP:

```bash
ssh -i "C:\ssh\oracle.key" ubuntu@167.234.255.247
```

Se for a primeira conexao, o Windows pode perguntar se voce confia no host. Digite:

```text
yes
```

Depois disso, voce deve entrar na VM.

### Se o comando `ssh` nao funcionar no Windows

Hoje o Windows 10 e 11 normalmente ja trazem `OpenSSH Client`, mas se der erro de comando inexistente:

1. Abra `Configuracoes`
2. Va em `Aplicativos`
3. Abra `Recursos opcionais`
4. Procure por `OpenSSH Client`
5. Instale se ainda nao estiver ativo

### Alternativa com PuTTY

Se voce preferir interface grafica, tambem pode usar o `PuTTY`, mas o tutorial principal segue com `PowerShell`, porque e mais simples e nao exige instalar outra ferramenta.

### Se a chave der erro de permissao no Windows

Esse erro costuma aparecer assim:

```text
UNPROTECTED PRIVATE KEY FILE
Permissions for '...oracle.key' are too open
```

Ou assim:

```text
Load key "...oracle.key": Permission denied
```

Quando isso acontecer, faca assim no `PowerShell`:

1. Crie uma pasta simples fora do `OneDrive`

```powershell
mkdir C:\ssh -Force
```

2. Copie sua chave privada para la

```powershell
copy "C:\Users\SEU_USUARIO\CAMINHO\ssh-key.key" "C:\ssh\oracle.key"
```

3. Ajuste as permissoes do arquivo

```powershell
$k = "C:\ssh\oracle.key"
$user = "$env:USERDOMAIN\$env:USERNAME"

icacls $k /inheritance:r
icacls $k /grant "${user}:(F)"
icacls $k /remove "Users"
icacls $k /remove "Authenticated Users"
icacls $k /remove "Everyone"
icacls $k /remove "Lucas\CodexSandboxUsers"
icacls $k
```

4. Tente conectar novamente

```powershell
ssh -i "C:\ssh\oracle.key" ubuntu@IP_PUBLICO
```

Observacoes:

- use a chave privada, ou seja, o arquivo sem `.pub`
- a chave publica `.pub` nao serve para conectar
- o ideal e que o arquivo fique acessivel apenas para o seu usuario

### Observacao sobre o usuario da VM

- em imagens Ubuntu, o usuario costuma ser `ubuntu`
- em algumas imagens Oracle Linux, o usuario costuma ser `opc`

## 6. Atualizar o servidor

```bash
sudo apt update && sudo apt upgrade -y
```

## 7. Instalar os pacotes necessarios

```bash
sudo apt install -y software-properties-common unzip curl git nginx sqlite3
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-fpm php8.3-mbstring php8.3-xml php8.3-curl php8.3-sqlite3 php8.3-zip php8.3-bcmath php8.3-intl
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
```

## 8. Publicar o codigo

Uma estrutura simples:

```bash
cd /var/www
sudo mkdir -p /var/www/kuantagrana
sudo chown -R $USER:$USER /var/www/kuantagrana
git clone SEU_REPOSITORIO /var/www/kuantagrana
cd /var/www/kuantagrana
```

Se o seu repositorio estiver publico no GitHub, o comando fica assim:

```bash
git clone https://github.com/SEU_USUARIO/SEU_REPOSITORIO.git /var/www/kuantagrana
```

Importante:

- essa etapa baixa o projeto de um repositorio remoto, nao do seu PC
- se o repositorio for privado, o mais pratico e configurar `SSH` ou usar um `token`
- se voce so quer simplificar o primeiro deploy, deixar o repositorio publico temporariamente pode ajudar

Se o repositorio ja existir:

```bash
cd /var/www/kuantagrana
git pull origin main
```

## 9. Instalar dependencias do projeto

```bash
cd /var/www/kuantagrana
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

## 10. Configurar o arquivo .env

```bash
cp .env.example .env
php artisan key:generate
```

Edite o `.env`:

```env
APP_NAME=KuantaGrana
APP_ENV=production
APP_DEBUG=false
APP_URL=http://SEU_DOMINIO_OU_IP

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/kuantagrana/database/database.sqlite

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=log
```

Se voce ainda nao tiver dominio, use o IP publico da VM.

Exemplo:

```env
APP_URL=http://167.234.255.247
```

Crie o banco SQLite:

```bash
touch /var/www/kuantagrana/database/database.sqlite
```

## 11. Rodar migrations e seeders

```bash
cd /var/www/kuantagrana
php artisan migrate --force
php artisan db:seed --force
```

## 12. Ajustar permissoes

```bash
sudo chown -R ubuntu:ubuntu /var/www/kuantagrana
cd /var/www/kuantagrana
sudo chgrp -R www-data storage bootstrap/cache database
sudo find storage -type d -exec chmod 775 {} \;
sudo find storage -type f -exec chmod 664 {} \;
sudo find bootstrap/cache -type d -exec chmod 775 {} \;
sudo find bootstrap/cache -type f -exec chmod 664 {} \;
sudo chown ubuntu:www-data database/database.sqlite
sudo chmod 664 database/database.sqlite
```

Esse ajuste evita erro como:

```text
The stream or file "/var/www/kuantagrana/storage/logs/laravel.log" could not be opened in append mode
file_put_contents(/var/www/kuantagrana/bootstrap/cache/config.php): Failed to open stream: Permission denied
```

Esse modelo tambem evita erro como:

```text
error: cannot open '.git/FETCH_HEAD': Permission denied
```

Porque:

- o codigo e a pasta `.git` ficam com o usuario `ubuntu`
- o Laravel continua podendo escrever em `storage`, `bootstrap/cache` e `database`

## 13. Configurar o Nginx

Crie o arquivo:

```bash
sudo nano /etc/nginx/sites-available/kuantagrana
```

Conteudo:

```nginx
server {
    listen 80;
    server_name SEU_DOMINIO_OU_IP;
    root /var/www/kuantagrana/public;

    index index.php index.html;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Importante:

- no `server_name`, use apenas o dominio ou o IP
- nao coloque `http://` nem `https://`

Exemplo correto:

```nginx
server_name 167.234.255.247;
```

Ative o site:

```bash
sudo ln -s /etc/nginx/sites-available/kuantagrana /etc/nginx/sites-enabled/kuantagrana
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

Se o `nginx -t` mostrar algo como:

```text
server name "http://167.234.255.247" has suspicious symbols
```

isso significa que voce colocou `http://` dentro de `server_name`. Corrija para apenas o IP ou dominio.

## 14. Otimizar o Laravel para producao

```bash
cd /var/www/kuantagrana
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Se quiser confirmar que o `APP_URL` foi aplicado corretamente:

```bash
php artisan tinker --execute="dump(config('app.url'));"
```

## 15. Configurar HTTPS

Quando o dominio e o `www` ja estiverem apontando para o IP da VM:

1. ajuste o `.env`

```bash
cd /var/www/kuantagrana
nano .env
```

Exemplo:

```env
APP_URL=http://kuantagrana.com.br
```

2. aplique a configuracao

```bash
php artisan optimize:clear
php artisan config:cache
```

3. ajuste o `server_name` no Nginx para atender os dois hosts

```nginx
server_name kuantagrana.com.br www.kuantagrana.com.br;
```

4. teste e recarregue o Nginx

```bash
sudo nginx -t
sudo systemctl reload nginx
```

5. confirme se os dois nomes resolvem para o IP da VM

```bash
getent hosts kuantagrana.com.br
getent hosts www.kuantagrana.com.br
```

Ou no Windows:

```powershell
nslookup kuantagrana.com.br
nslookup www.kuantagrana.com.br
```

6. instale e rode o Certbot

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d kuantagrana.com.br -d www.kuantagrana.com.br
```

Se ainda nao tiver dominio, pode pular essa etapa por enquanto.

Nesse caso:

- mantenha `APP_URL=http://IP_PUBLICO`
- teste o sistema pelo navegador usando o IP da VM
- deixe o `HTTPS` para quando tiver um dominio apontado corretamente

### O que escolher no Certbot

Durante a execucao do Certbot:

- informe seu e-mail
- aceite os termos
- se ele perguntar sobre redirecionar HTTP para HTTPS, escolha a opcao de redirecionar

### Estado final aceitavel

Se, no fim, estiver funcionando assim:

- `https://kuantagrana.com.br`
- `https://www.kuantagrana.com.br`

entao isso ja esta bom e pronto para uso.

Voce nao precisa obrigatoriamente fazer mais nada alem disso.

### Opcional: escolher uma URL canonica

Se quiser um acabamento mais profissional, depois voce pode escolher uma URL principal:

- manter `kuantagrana.com.br` como principal e redirecionar `www`
- ou manter `www.kuantagrana.com.br` como principal e redirecionar o dominio raiz

Isso e opcional. Se os dois enderecos estiverem abrindo com HTTPS, o sistema ja esta corretamente publicado.

## 16. Atualizacao futura do sistema

Quando voce fizer mudancas no projeto:

```bash
cd /var/www/kuantagrana
git stash push -m "backup antes do deploy"
git pull origin main
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl reload php8.3-fpm
sudo systemctl reload nginx
```

### Quando basta fazer isso para as mudancas aparecerem

Na pratica, sim: esse fluxo e o que faz as alteracoes aparecerem no sistema hospedado.

O papel de cada etapa e:

- `git pull`: traz o codigo novo
- `composer install`: instala dependencias PHP novas, se houver
- `npm install` e `npm run build`: atualizam os assets do frontend
- `php artisan migrate --force`: aplica mudancas no banco
- `optimize:clear` e os caches: garantem que o Laravel passe a usar a versao atual
- `reload` dos servicos: finaliza a atualizacao com mais seguranca

### Se o `git pull` reclamar de alteracoes locais

Erro comum:

```text
Your local changes to the following files would be overwritten by merge
```

Se voce so quer guardar essas alteracoes e seguir com o deploy:

```bash
git stash push -m "backup antes do pull"
git pull origin main
```

Para ver o que foi guardado:

```bash
git stash list
```

Se voce tiver certeza de que quer descartar as alteracoes locais de alguns arquivos:

```bash
git restore CAMINHO_DO_ARQUIVO
git pull origin main
```

## 17. Firewall local da VM

Em imagens Ubuntu da Oracle, pode acontecer de a rede da Oracle estar correta, mas o firewall local da VM continuar bloqueando tudo, exceto `SSH`.

Um sintoma comum e este:

- `curl -I http://127.0.0.1` funciona
- `curl -I http://IP_PUBLICO` falha
- a `Security List` da Oracle ja esta com porta `80` liberada

Se isso acontecer, confira:

```bash
sudo iptables -S
```

Se aparecer algo parecido com isto:

```text
-A INPUT -p tcp -m state --state NEW -m tcp --dport 22 -j ACCEPT
-A INPUT -j REJECT --reject-with icmp-host-prohibited
```

entao a propria VM esta bloqueando `80` e `443`.

Libere com:

```bash
sudo iptables -I INPUT 4 -p tcp --dport 80 -j ACCEPT
sudo iptables -I INPUT 5 -p tcp --dport 443 -j ACCEPT
```

Depois teste:

```bash
curl -I http://IP_PUBLICO
```

Para persistir essas regras apos reiniciar a VM:

```bash
sudo apt update
sudo apt install -y iptables-persistent
sudo netfilter-persistent save
```

Se a instalacao perguntar se deseja salvar as regras atuais, responda `Yes`.

## 18. Observacoes importantes para este projeto

- O projeto usa `SQLite` por padrao, o que simplifica bastante o primeiro deploy.
- Como `SESSION_DRIVER`, `CACHE_STORE` e `QUEUE_CONNECTION` estao em `database`, as migrations precisam estar rodadas corretamente.
- O sistema possui rota de health check em `/up`, que pode ser usada para teste rapido depois do deploy.
- Se futuramente o uso crescer, o proximo passo natural e migrar de `SQLite` para `PostgreSQL` ou `MySQL`.

## 19. Checklist final

Depois do deploy, valide:

- a pagina inicial abre
- o site abre pelo `IP publico` mesmo sem dominio
- o site abre por `https://kuantagrana.com.br`
- o site abre por `https://www.kuantagrana.com.br`
- cadastro e login funcionam
- `/up` responde
- migrations e seeders rodaram
- assets do Vite foram gerados com `npm run build`
- a pasta `storage` esta gravavel
- o arquivo `database/database.sqlite` existe e tem permissao correta
- `curl -I http://127.0.0.1` responde dentro da VM
- `curl -I http://IP_PUBLICO` tambem responde dentro da VM
- o `iptables` local nao esta bloqueando as portas `80` e `443`

## 20. Troubleshooting rapido

### O SSH no Windows abre uma janela em vez de conectar

Voce provavelmente tentou abrir o arquivo da chave em vez de rodar o comando `ssh`.

O formato correto e:

```powershell
ssh -i "C:\ssh\oracle.key" ubuntu@IP_PUBLICO
```

### O GitHub recusou `git clone` com senha

Erro comum:

```text
Invalid username or token. Password authentication is not supported for Git operations.
```

Hoje o GitHub nao aceita senha comum para repositorio privado via HTTPS.

Solucoes:

- deixar o repositorio publico para o primeiro deploy
- usar `SSH`
- usar `Personal Access Token`

### O Laravel abre localmente, mas nao abre pelo IP publico

Cheque nesta ordem:

1. `Security List` da Oracle com portas `22`, `80` e `443`
2. `Route Table` com `0.0.0.0/0` para `Internet Gateway`
3. `Primary VNIC` com `Public IPv4 address`
4. `iptables` local da VM

### O Laravel abre, mas os assets apontam para `127.0.0.1`

Confirme:

```bash
grep APP_URL .env
php artisan optimize:clear
php artisan config:cache
php artisan tinker --execute="dump(config('app.url'));"
```

### O `config:cache` falha com `Permission denied`

Isso normalmente significa que `storage/` ou `bootstrap/cache/` nao estao gravaveis.

Refaca a etapa de permissoes do passo `12`.

### O dominio abre, mas o Certbot falha

Cheque nesta ordem:

1. `kuantagrana.com.br` resolve para o IP da VM
2. `www.kuantagrana.com.br` resolve para o IP da VM
3. a porta `80` continua aberta na Oracle e no `iptables`
4. o `server_name` inclui os dois hosts

## 21. Referencias oficiais usadas nesta versao do tutorial

- Criacao de instancia na Console: https://docs.oracle.com/pt-br/iaas/Content/Compute/Tasks/launchinginstance.htm
- Tutorial oficial de primeira instancia Linux: https://docs.oracle.com/iaas/Content/Compute/tutorials/first-linux-instance/overview.htm
- Recursos Always Free: https://docs.oracle.com/en-us/iaas/Content/FreeTier/freetier_topic-Always_Free_Resources.htm
