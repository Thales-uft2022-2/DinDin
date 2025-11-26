UFT- Campus Palmas
Disciplina: Engenharia de Software 2025/2
Professor: Dr Edeilson Milhomem da Silva

Grupo: DinDIn 
Integrantes: Cristian, Gabriel Portuguez, Thales, Vinicius Fernandes.
# DinDin 💸

**DinDin** é um aplicativo web simples e intuitivo para **gerenciamento de finanças pessoais e empresariais**.  
O foco é ajudar o usuário a **controlar receitas e despesas**, organizar em **categorias personalizadas** e manter uma visão clara do **saldo mensal**.

Com o **DinDin**, você pode:
- Criar sua conta e acessar de forma segura 🔐
- Registrar receitas e despesas de maneira rápida 💵
- Organizar seus lançamentos por categorias 📂
- Consultar histórico de transações e filtros 🔎
- Acompanhar saldo do mês em tempo real 📊  

👉 O objetivo do MVP é oferecer **controle financeiro prático e acessível**, entregando valor desde o primeiro uso, sem burocracia.

---
LandingPage: [Clique](https://thales-uft2022-2.github.io/DinDin/)
---
[Apresentação Final da Disciplina](https://www.canva.com/design/DAG5JPbcfXk/gmwWmThA4KlkFPPQw_-PEA/edit?utm_content=DAG5JPbcfXk&utm_campaign=designshare&utm_medium=link2&utm_source=sharebutton)
---
🛠️ Guia de Configuração e Instalação (Developer Setup)

Este documento contém as instruções passo a passo para configurar o ambiente de desenvolvimento do projeto DinDin em sua máquina local.

📋 Pré-requisitos

Antes de começar, certifique-se de ter as seguintes ferramentas instaladas:

XAMPP (ou similar com Apache e MySQL) - Download

Recomendado: PHP 8.1 ou superior.

Composer (Gerenciador de dependências do PHP) - Download

Git - Download

Editor de Código (VS Code recomendado).

🚀 Passo a Passo da Instalação

1. Clonar o Repositório

Abra seu terminal (Git Bash ou CMD) e clone o projeto para a sua máquina.

Se você estiver usando XAMPP, o ideal é clonar diretamente dentro da pasta htdocs.

cd C:\xampp\htdocs
git clone [https://github.com/Thales-uft2022-2/DinDin](https://github.com/Thales-uft2022-2/DinDin)
cd DinDin


2. Instalar Dependências (Backend)

O projeto utiliza o Composer para gerenciar bibliotecas (como o PHPMailer e o PHPUnit). Na raiz do projeto (dentro da pasta DinDin), execute:

composer install


Isso criará a pasta vendor/ com todas as bibliotecas necessárias.

3. Configurar o Banco de Dados

Inicie o Apache e o MySQL no painel de controle do XAMPP.

Acesse o phpMyAdmin no navegador: http://localhost/phpmyadmin.

Crie um novo banco de dados chamado dindin (ou o nome definido no seu config.php).

Collation recomendada: utf8mb4_unicode_ci.

Importe o esquema do banco:

Selecione o banco criado.

Vá na aba Importar.

Escolha o arquivo dindin.sql localizado na raiz do projeto.

Clique em Executar.

4. Configurar Variáveis de Ambiente

Verifique o arquivo config/config.php (ou crie um arquivo .env se o projeto utilizar) para garantir que as credenciais do banco estão corretas para o seu ambiente XAMPP padrão.

Exemplo padrão do XAMPP:

Host: localhost

User: root

Password: `` (vazio)

Database: dindin

5. Executar o Projeto

Com o Apache do XAMPP rodando e os arquivos na pasta htdocs, acesse o projeto pelo navegador:

http://localhost/DinDin/public


Nota: O ponto de entrada da aplicação é a pasta /public. Se você acessar apenas /DinDin, navegue até a pasta public.

🧪 Rodando os Testes (PHPUnit)

Para garantir que tudo está funcionando corretamente, execute os testes unitários.

No terminal, na raiz do projeto:

./vendor/bin/phpunit


(No Windows, pode ser necessário usar vendor\bin\phpunit)

Se todos os testes passarem (ficar verde), seu ambiente está configurado e pronto para o desenvolvimento! ✅

📂 Estrutura de Pastas Importantes

app/ - Lógica do sistema (Controllers, Models, Services).

public/ - Arquivos acessíveis publicamente (CSS, JS, Index.php).

config/ - Arquivos de configuração (Banco de dados, etc).

views/ - Telas e templates HTML/PHP.

tests/ - Testes unitários.

---
# Planejamento de Sprints

## Sprint 1: Transações

**Valor:** Registrar, gerenciar e consultar transações financeiras.

### Tarefas:

- **Cadastro de transações:**  
  Como usuário, eu quero cadastrar uma transação de receita ou despesa com valor, data, categoria e descrição para manter meu controle financeiro organizado.

- **Listagem de transações:**  
  Como usuário, eu quero visualizar uma lista de todas as minhas transações ordenadas por data para acompanhar meu histórico financeiro.

- **Filtragem e busca:**  
  Como usuário, eu quero filtrar minhas transações por período de datas, tipo e categoria (receita ou despesa) para encontrar rapidamente informações específicas.

- **Edição de transações:**  
  Como usuário, eu quero editar uma transação existente para corrigir informações incorretas.

- **Exclusão de transações:**  
  Como usuário, eu quero excluir uma transação indevida para manter meu histórico atualizado e limpo.

---

## Sprint 2: Usuário & Autenticação

**Valor:** Prover acesso seguro e confiável.

### Tarefas:

- **Criação de conta:**  
  Como visitante, eu quero criar uma conta com e-mail e senha para começar a usar a plataforma.

- **Login:**  
  Como usuário cadastrado, eu quero realizar login com minhas credenciais ou como visitante, fazer login com minha conta Google para acessar minha conta de forma segura.

- **Logout:**  
  Como usuário, eu quero encerrar minha sessão para garantir a segurança da minha conta.

- **Recuperação de senha:**  
  Como usuário que esqueceu a senha, eu quero redefini-la através de um fluxo seguro para recuperar o acesso à minha conta.
---
## Sprint 3: Refatoração, Camada de Serviço (APIs) e Testes

**Valor:** Aumentar a modularidade e a manutenibilidade do código, desacoplando as regras de negócio da camada de controle, preparando a aplicação para futuras integrações e garantindo a qualidade das funcionalidades através de testes automatizados.

### Tarefas:

- **Refatoração da Gestão de Transações:**
  Como desenvolvedor, eu quero mover a lógica de negócio das transações (cadastro, listagem, edição, exclusão) para uma camada de serviço com APIs, para desacoplar o código e facilitar os testes.

- **Refatoração de Usuário & Autenticação:**
  Como desenvolvedor, eu quero mover a lógica de negócio de autenticação (registro, login, logout, recuperação de senha) para uma camada de serviço com APIs, para centralizar as regras de segurança e torná-las testáveis.

- **Implementação de Testes Unitários:**
  Como desenvolvedor, eu quero criar testes unitários para a nova camada de serviço, para garantir a qualidade, a confiabilidade e a estabilidade das regras de negócio.

- **Adaptação da Camada de Controle (Controllers):**
  Como desenvolvedor, eu quero ajustar os controllers existentes para que eles consumam a nova camada de serviço, garantindo que a interface continue funcionando como esperado.

---
# 📊 Sprint 4 — Dashboard e Categorias

**Valor:** Entregar uma visão clara e rápida da **saúde financeira do usuário** através de um **dashboard**, e permitir **maior personalização** no controle de gastos com a **gestão de categorias**.

---

##  Tarefas

1. **Dashboard de Saldo Mensal**  
   **Como:** usuário  
   **Quero:** visualizar um dashboard na tela inicial com o saldo do mês (total de receitas, total de despesas e balanço)  
   **Para:** acompanhar meu desempenho financeiro em tempo real. 

2. **Gestão de Categorias Personalizadas (CRUD)**  
   **Como:** usuário  
   **Quero:** criar, visualizar, editar e excluir minhas próprias categorias  
   **Para:** organizar meus lançamentos financeiros de acordo com a minha realidade. 

3. **Integração das Categorias no Lançamento**  
   **Como:** usuário  
   **Quero:** que o formulário de "Cadastrar Transação" (Sprint 1) utilize as categorias que eu criei  
   **Para:** classificar minhas receitas e despesas corretamente.

4. **Implementação de Testes Unitários (Backend)**  
   **Como:** desenvolvedor  
   **Quero:** criar testes unitários para as regras de negócio (cálculo de saldo do dashboard e CRUD de categorias)  
   **Para:** garantir a confiabilidade e a corretude dos dados.

---


### Features
## 📌 Sprint 1 — Transações

### US-Tx-01 — Adicionar Transação
**Como** usuário autenticado  
**Quero** cadastrar uma transação (receita ou despesa) com valor, data, categoria e descrição  
**Para** registrar meu fluxo financeiro  

**Critérios de Aceite**
- Dado que estou no formulário de nova transação  
  Quando seleciono tipo (receita/ despesa), informo valor > 0 e data (padrão = hoje)  
  Então posso salvar a transação com sucesso.  
- Dado que deixo campos obrigatórios vazios ou inválidos  
  Quando tento salvar  
  Então vejo mensagens de validação junto aos campos.  

---

### US-Tx-02 — Listar Transações
**Como** usuário autenticado  
**Quero** ver uma lista das minhas transações em ordem por data  
**Para** consultar meu histórico  

**Critérios de Aceite**
- Dado que tenho transações cadastradas  
  Quando acesso a página de histórico  
  Então vejo uma lista com data, tipo, categoria, descrição e valor.  
- Dado que não tenho transações  
  Quando acesso o histórico  
  Então vejo um estado vazio com instrução para adicionar a primeira transação.  

---

### US-Tx-03 — Filtrar/Buscar Transações
**Como** usuário autenticado  
**Quero** filtrar por período e tipo (receita/ despesa)  
**Para** encontrar rapidamente o que preciso  

**Critérios de Aceite**
- Dado que seleciono um intervalo de datas  
  Quando aplico o filtro  
  Então a lista mostra apenas transações nesse período.  
- Dado que escolho um tipo específico  
  Quando aplico o filtro  
  Então a lista mostra apenas receitas ou apenas despesas.  

---

### US-Tx-04 — Editar Transação
**Como** usuário autenticado  
**Quero** editar uma transação existente  
**Para** corrigir informações registradas incorretamente  

**Critérios de Aceite**
- Dado que estou na lista  
  Quando clico em “Editar” de uma transação  
  Então vejo o formulário preenchido e posso alterar e salvar.  
- Dado que salvo alterações válidas  
  Quando confirmo  
  Então vejo uma mensagem de sucesso e a lista atualizada.  

---

### US-Tx-05 — Excluir Transação
**Como** usuário autenticado  
**Quero** remover uma transação  
**Para** apagar lançamentos indevidos  

**Critérios de Aceite**
- Dado que estou na lista  
  Quando clico em “Excluir”  
  Então vejo um pedido de confirmação.  
- Dado que confirmo a exclusão  
  Quando finalizo  
  Então a transação some da lista e vejo mensagem de sucesso.  

---

## 📌 Sprint 2 — Usuário & Autenticação
### US-Auth-01 — Registro de Usuário
**Como visitante, eu quero criar uma conta informando e-mail e senha, para acessar o sistema com meus próprios dados.**

**Critérios de Aceite:**
- **Dado** que estou na página de registro,  
  **Quando** preencho e-mail válido e senha (mín. 8 caracteres) e envio,  
  **Então** minha conta é criada e sou direcionado(a) para a página inicial logado(a).
  
- **Dado** que já existe um usuário com esse e-mail,  
  **Quando** tento registrar novamente,  
  **Então** vejo uma mensagem “e-mail já cadastrado”.

---

### US-Auth-02 — Login
**Como usuário cadastrado ou visitante, eu quero entrar com e-mail e senha ou com minha conta Google para acessar minhas funcionalidades.**

**Critérios de Aceite:**
- **Dado** que informo credenciais válidas (e-mail e senha) ou faço login com minha conta Google,  
  **Quando** envio o formulário,  
  **Então** sou autenticado(a) e vejo a página inicial.
  
- **Dado** que sou um visitante,  
  **Quando** tento fazer login com minha conta Google pela primeira vez,  
  **Então** uma conta será criada automaticamente com o e-mail do Google e serei redirecionado(a) para a página inicial.

- **Dado** que sou um usuário que já tem uma conta registrada com o e-mail do Google,  
  **Quando** faço login com a conta Google,  
  **Então** sou autenticado(a) e redirecionado(a) para a página inicial.

- **Dado** que informo credenciais inválidas (e-mail e senha),  
  **Quando** envio o formulário,  
  **Então** vejo uma mensagem clara de erro sem revelar detalhes de segurança.
---

### US-Auth-03 — Logout
**Como usuário autenticado, eu quero sair da minha sessão para encerrar o acesso em dispositivos compartilhados.**

**Critérios de Aceite:**
- **Dado** que estou logado(a),  
  **Quando** clico em “Sair”,  
  **Então** minha sessão é finalizada e sou enviado(a) para a página de login.

---

### US-Auth-04 — Recuperar Senha
**Como usuário, eu quero receber um link/código para redefinir a senha para recuperar o acesso quando eu esquecer.**

**Critérios de Aceite:**
- **Dado** que informo um e-mail cadastrado,  
  **Quando** solicito “Esqueci minha senha”,  
  **Então** recebo instruções claras de redefinição.
  
- **Dado** que informo e-mail não cadastrado,  
  **Quando** solicito redefinição,  
  **Então** vejo mensagem genérica (“Se existir, enviaremos instruções”), sem vazar existência da conta.

---
## 📌 Sprint 3 — Refatoração, Camada de Serviço (APIs) e Testes

### TS-Svc-01 — Criar API e Serviço para Cadastro de Transação
**Como** desenvolvedor,
**Quero** refatorar a lógica de cadastro de transação para uma camada de serviço exposta por uma API (`POST /api/transactions`),
**Para** desacoplar a lógica de negócio do controller e criar testes unitários.

---

### TS-Svc-02 — Criar API e Serviço para Listagem e Filtragem de Transações
**Como** desenvolvedor,
**Quero** refatorar a busca e filtragem de transações para uma camada de serviço exposta por uma API (`GET /api/transactions`),
**Para** centralizar as regras de consulta e testá-las de forma isolada.

---

### TS-Svc-03 — Criar API e Serviço para Edição de Transação
**Como** desenvolvedor,
**Quero** refatorar a lógica de edição de transação para uma camada de serviço exposta por uma API (`PUT /api/transactions/{id}`),
**Para** garantir que as regras de atualização sejam consistentes e testáveis.

---

### TS-Svc-04 — Criar API e Serviço para Exclusão de Transação
**Como** desenvolvedor,
**Quero** refatorar a lógica de exclusão de transação para uma camada de serviço exposta por uma API (`DELETE /api/transactions/{id}`),
**Para** isolar esta operação crítica e cobri-la com testes.

---

### TS-Auth-01 — Criar API e Serviço para Registro de Usuário
**Como** desenvolvedor,
**Quero** criar uma camada de serviço e uma API (`POST /api/auth/register`) para o registro de novos usuários,
**Para** separar as regras de criação de conta e testá-las de forma independente.

---

### TS-Auth-02 — Criar API e Serviço para Login
**Como** desenvolvedor,
**Quero** mover a lógica de autenticação (e-mail/senha e Google) para um `AuthService` e uma API (`POST /api/auth/login`),
**Para** centralizar e testar os mecanismos de autenticação.

---

### TS-Auth-03 — Criar API e Serviço para Logout
**Como** desenvolvedor,
**Quero** gerenciar o encerramento de sessão através de um `AuthService` e uma API (`POST /api/auth/logout`),
**Para** padronizar o processo de logout.

---

### TS-Auth-04 — Criar API e Serviço para Recuperação de Senha
**Como** desenvolvedor,
**Quero** refatorar o fluxo de recuperação de senha para um `AuthService` e APIs correspondentes,
**Para** isolar e testar essa funcionalidade de segurança crítica.

---
## 📌 Sprint 4 — Dashboard e Categorias

### US-Dash-01 — Visualizar Saldo Mensal
**Como** usuário autenticado  
**Quero** visualizar um dashboard na tela inicial com o saldo do mês (total de receitas, total de despesas e balanço)  
**Para** acompanhar meu desempenho financeiro em tempo real.

**Critérios de Aceite**
- Dado que estou na página inicial (dashboard) e tenho transações no mês corrente, quando a página carrega, então vejo três valores claros: **Total de Receitas (mês)**, **Total de Despesas (mês)** e **Balanço (Receitas - Despesas)**.
- Dado que não tenho transações no mês corrente, quando acesso o dashboard, então vejo os valores zerados (R$ 0,00) ou um estado indicando **"Sem movimentações"**.
- Dado que adiciono uma nova transação do mês corrente, quando retorno ao dashboard, então os valores do saldo mensal são atualizados.

---

### US-Cat-01 — Criar Categoria
**Como** usuário autenticado  
**Quero** criar novas categorias (ex: "Alimentação", "Transporte")  
**Para** classificar meus gastos de forma personalizada.

**Critérios de Aceite**
- Ao clicar em **"Nova Categoria"**, inserir um nome válido e salvar → a categoria aparece na lista com mensagem de sucesso.
- Tentar salvar sem nome ou com nome já existente → exibir mensagem de erro ("Nome é obrigatório" / "Categoria já existe").

---

### US-Cat-02 — Listar Categorias
**Como** usuário autenticado  
**Quero** ver uma lista de todas as minhas categorias personalizadas  
**Para** saber como estou organizando meus lançamentos.

**Critérios de Aceite**
- Se houver categorias cadastradas → exibir lista com opções **Editar** e **Excluir**.
- Se não houver categorias → exibir estado vazio com instrução para adicionar a primeira categoria.

---

### US-Cat-03 — Editar Categoria
**Como** usuário autenticado  
**Quero** editar o nome de uma categoria existente  
**Para** corrigir erros de digitação ou reclassificar.

**Critérios de Aceite**
- Editar nome e salvar → ver nome atualizado e mensagem de sucesso.
- Tentar renomear para nome já existente → exibir erro "Categoria já existe".

---

### US-Cat-04 — Excluir Categoria
**Como** usuário autenticado  
**Quero** excluir uma categoria que não uso mais  
**Para** manter minha lista de categorias limpa.

**Critérios de Aceite**
- Clicar em **Excluir** → pedir confirmação.
- Confirmar exclusão → remover e mostrar mensagem de sucesso.
- Se categoria estiver ligada a transações → ao excluir, atualizar transações associadas (ex: "Sem Categoria" ou `null`).

---

### US-Tx-06 — Usar Categorias Personalizadas no Lançamento
**Como** usuário autenticado  
**Quero** selecionar uma categoria personalizada ao cadastrar/editar uma transação  
**Para** classificar corretamente o lançamento.

**Critérios de Aceite**
- No formulário de **Adicionar/Editar Transação**, o campo **Categoria** deve apresentar um dropdown com todas as categorias criadas.
- Selecionar categoria e salvar → exibir a categoria na listagem de transações.

---

### TS-Test-01 — Testes Unitários do Serviço de Dashboard
**Como** desenvolvedor  
**Quero** criar testes unitários para a camada de serviço que calcula o saldo do dashboard  
**Para** garantir que os valores exibidos estão corretos e seguros.

---

### TS-Test-02 — Testes Unitários do Serviço de Categorias (CRUD)
**Como** desenvolvedor  
**Quero** criar testes unitários para a camada de serviço que gerencia o CRUD de Categorias  
**Para** garantir integridade dos dados e isolamento entre usuários.

---
## 📌 Sprint 5 — Analytics, Perfil e Administração

### US-Analytics-01 — Gráfico de Despesas por Categoria 📊
**Como usuário, eu quero ver um gráfico (pizza ou rosca) no dashboard que detalha meus gastos por categoria no período selecionado, para entender rapidamente para onde meu dinheiro está indo.**

**Critérios de Aceite:**
- **Dado** que estou na página inicial (dashboard),  
  **Quando** existem despesas cadastradas no período selecionado,  
  **Então** vejo um gráfico do tipo pizza/rosça mostrando a distribuição percentual dos gastos por categoria.
  
- **Dado** que não há despesas no período selecionado,  
  **Quando** visualizo o dashboard,  
  **Então** vejo um estado vazio no gráfico com a mensagem "Nenhuma despesa no período".

---

### US-Analytics-02 — Gráfico de Evolução Financeira 📈
**Como usuário, eu quero ver um gráfico de linha no dashboard que mostra o total de Receitas vs. Despesas dos últimos 6 meses, para acompanhar minha evolução financeira e identificar tendências.**

**Critérios de Aceite:**
- **Dado** que estou na página inicial (dashboard),  
  **Quando** existem transações nos últimos 6 meses,  
  **Então** vejo um gráfico de linha com duas séries: Receitas (verde) e Despesas (vermelho) ao longo do tempo.
  
- **Dado** que não há transações suficientes,  
  **Quando** visualizo o gráfico,  
  **Então** vejo os meses disponíveis ou estado vazio com mensagem "Dados insuficientes".

---

### US-Profile-01 — Página de Perfil (Visualização) 👤
**Como usuário, eu quero acessar uma página de "Perfil" onde posso visualizar meus dados cadastrais (nome, e-mail), para consultar minhas informações e preparar para futuras edições.**

**Critérios de Aceite:**
- **Dado** que estou logado e acesso a página "Meu Perfil",  
  **Quando** a página carrega,  
  **Então** vejo meus dados cadastrais: nome completo e e-mail.
  
- **Dado** que meus dados estão incompletos,  
  **Quando** visualizo o perfil,  
  **Então** vejo campos vazios ou com valores padrão.

---

### US-Profile-02 — Página de Perfil (Alteração de Senha) 🔐
**Como usuário, eu quero poder alterar minha senha com segurança através da minha página de Perfil, para manter minha conta segura e atualizar minhas credenciais.**

**Critérios de Aceite:**
- **Dado** que informo minha senha atual correta e uma nova senha válida (mín. 8 caracteres),  
  **Quando** confirmo a alteração,  
  **Então** minha senha é atualizada e recebo uma notificação de sucesso.
  
- **Dado** que informo a senha atual incorreta,  
  **Quando** tento alterar a senha,  
  **Então** vejo mensagem de erro "Senha atual incorreta".
  
- **Dado** que a nova senha não atende aos requisitos mínimos,  
  **Quando** tento salvar,  
  **Então** vejo mensagem explicativa sobre os requisitos.

---

### TS-Admin-01 — Estrutura de Permissão de Admin (Backend)
**Como desenvolvedor, eu quero implementar uma lógica de role (função/permissão) no backend (ex: "USER" e "ADMIN") e um middleware de segurança, para proteger rotas e funcionalidades que só administradores podem acessar.**

**Critérios de Aceite:**
- **Dado** que um usuário comum tenta acessar uma rota administrativa,  
  **Quando** o middleware verifica suas permissões,  
  **Então** o acesso é negado com status 403.
  
- **Dado** que um administrador acessa uma rota administrativa,  
  **Quando** o middleware valida sua role,  
  **Então** o acesso é permitido.

---

### US-Admin-01 — Página de Administração (Listagem de Usuários) 👑
**Como Administrador, eu quero acessar uma página /admin protegida, que lista todos os usuários cadastrados no sistema (nome, e-mail, data de cadastro), para ter uma visão geral de quem está usando a plataforma.**

**Critérios de Aceite:**
- **Dado** que sou administrador e acesso /admin,  
  **Quando** a página carrega,  
  **Então** vejo uma tabela paginada com todos os usuários: nome, e-mail e data de cadastro.
  
- **Dado** que não sou administrador,  
  **Quando** tento acessar /admin,  
  **Então** sou redirecionado com mensagem de acesso negado.

---

### TS-Test-03 — Testes Unitários (Serviços de Analytics e Perfil) 🛠️
**Como desenvolvedor, eu quero criar testes unitários para os novos serviços (cálculo de dados para gráficos, alteração de senha, listagem de usuários), para garantir que os dados analíticos e as operações de usuário/admin são seguras e corretas.**

**Critérios de Aceite:**
- **Dado** um conjunto de transações de teste,  
  **Quando** executo o serviço de cálculo de analytics,  
  **Então** os valores retornados para os gráficos estão corretos.
  
- **Dado** uma solicitação de alteração de senha válida,  
  **Quando** executo o serviço de perfil,  
  **Então** a senha é criptografada e atualizada no banco.
  
- **Dado** uma solicitação de listagem de usuários por admin,  
  **Quando** executo o serviço administrativo,  
  **Então** retorna apenas os dados permitidos pela política de segurança.
---
# 📌 DIVISÃO DAS TAREFAS

## 1° Sprint - Gestão de Transações

| Atividade                                  | Feature                 | Autor     | Revisor   |
|--------------------------------------------|-------------------------|-----------|-----------|
| Tela e função de cadastro de transação     | **US-Tx-01_cadastrar**  | Thales    | Caio      |
| Tela e função de listar transações         | **US-Tx-02_listar**     | Gabriel      | Cristian  |
| Tela e função de filtrar/buscar transações | **US-Tx-03_filtrar**    | Cristian  | Vinicius  |
| Tela e função de editar transação          | **US-Tx-04_editar**     | Vinicius  | Gabriel   |
| Botão e função de excluir transação        | **US-Tx-05_excluir**    | Cristian   | Thales    |

---

## 2° Sprint - Usuário & Autenticação

| Atividade                                  | Feature                 | Autor     | Revisor   |
|--------------------------------------------|-------------------------|-----------|-----------|
| Tela e função de registro de usuário       | **US-Auth-01_registro** | Gabriel   | Thales  |
| Tela e função de login                     | **US-Auth-02_login**    | Thales  | Cristian  |
| Função de logout                    | **US-Auth-03_logout**   | Vinicius  | Gabriel      |
| Função de recuperação de senha             | **US-Auth-05_recuperar**| Cristian    | Vinicius   |


### 3ª Sprint - Camada de Serviço, APIs e Testes
| Atividade | Feature (História Técnica) | Autor | Revisor |
| :--- | :--- | :--- | :--- |
| API e Serviço de Cadastro de Transação | TS-Svc-01 | Thales | Vinicius |
| API e Serviço de Listar/Filtrar Transações | TS-Svc-02 | Gabriel | Cristian |
| API e Serviço de Editar Transação | TS-Svc-03 | Vinicius | Gabriel |
| API e Serviço de Excluir Transação | TS-Svc-04 | Cristian | Thales |
| API e Serviço de Registro de Usuário | TS-Auth-01 | Gabriel | Vinicius |
| API e Serviço de Login de Usuário (inclui Google) | TS-Auth-02 | Thales | Cristian |
| API e Serviço de Logout | TS-Auth-03 | Vinicius | Gabriel |
| API e Serviço de Recuperação de Senha | TS-Auth-04 | Cristian | Thales |

---

### 4ª Sprint - Dashboard e Categorias
| Atividade                                      | Feature (História Técnica) | Autor     | Revisor  |
| :--- | :--- | :--- | :--- |
| Dashboard - Visualizar Saldo Mensal            | US-Dash-01                  | Gabriel  | Cristian  |
| Categorias - Criar Categoria                   | US-Cat-01                   | Gabriel  | Cristian  |
| Categorias - Listar Categorias                 | US-Cat-02                   | Cristian   | Gabriel |
| Categorias - Editar Categoria                  | US-Cat-03                   | Cristian  | Gabriel   |
| Categorias - Excluir Categoria                 | US-Cat-04                   | Vinicius   | Thales |
| Transações - Integrar Categorias Personalizadas| US-Tx-06                    | Vinicius  | Thales   |
| Testes - Serviço de Dashboard                  | TS-Test-01                  | Thales    | Vinicius |
| Testes - Serviço de Categorias (CRUD)          | TS-Test-02                  | Thales    | Vinicius |

---

## 5° Sprint - Analytics, Perfil e Administração

| Atividade                                      | Feature                     | Autor     | Revisor   |
|------------------------------------------------|-----------------------------|-----------|-----------|
| Gráfico de Despesas por Categoria              | **US-Analytics-01**         | Cristian  | Vinicius  |
| Gráfico de Evolução Financeira                 | **US-Analytics-02**         | Cristian  | Vinicius  |
| Página de Perfil (Visualização)                | **US-Profile-01**           | Gabriel   | Thales    |
| Página de Perfil (Alteração de Senha)          | **US-Profile-02**           | Gabriel   | Thales    |
| Estrutura de Permissão de Admin (Backend)      | **TS-Admin-01**             | Thales    | Cristian  |
| Página de Administração (Listagem de Usuários) | **US-Admin-01**             | Thales    | Cristian  |
| Testes Unitários (Analytics e Perfil)          | **TS-Test-03**              | Vinicius  | Gabriel   |
