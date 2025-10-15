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
