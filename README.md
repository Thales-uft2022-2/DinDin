UFT- Campus Palmas
Disciplina: Engenharia de Software 2025/2
Professor: Dr Edeilson Milhomem da Silva

Grupo: DinDIn 
Integrantes: Caio, Cristian, Gabriel Portuguez, Thales, Vinicius Fernandes.
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
| Tela e função de registro de usuário       | **US-Auth-06_registro** | Gabriel   | Thales  |
| Tela e função de login                     | **US-Auth-07_login**    | Thales  | Cristian  |
| Função de logout                           | **US-Auth-08_logout**   | Vinicius  | Gabriel      |
| Função de recuperação de senha             | **US-Auth-09_recuperar**| Cristian    | Vinicius   |

## 3ª Interação – MVC
| Atividade                                              | Feature              | Autor    | Revisor  |
| ------------------------------------------------------ | ------------------------------- | -------- | 
| Conexão entre páginas de Transação e Metas             | **US-TX-10_grafdashboard** |Cristian| Thales|
| Refatoração do código do projeto                       | **US-TX-11_grafdashboard** |Thales  | Gabriel|
| Padronização de layout (header/footer/navbar/partials) | **US-TX-12_layout_base**   |Gabriel | Vinicius|
| Middleware de autenticação (proteção de rotas)         | **US-TX-13_auth_guard**    |Vinicius| Cristian|
| Validações no backend + mensagens nas views            | **US-TX-14_validacao_servidor**|Cristian | Gabriel|
| Paginação/ordenação/busca server-side na listagem      | **US-TX-15_listagem_paginada**|Thales|Vinicius|
| ViewModels/DTOs para histórico e dashboard             | **US-TX-16_viewmodels**       |Gabriel| Thales|
| Serviço de agregação p/ gráficos (mês/categoria)       | **US-TX-17_service_graficos** |Vinicius| Gabriel|
| Repositório/DAO de Transações (abstração de PDO)       | **US-TX-18_repo_transacoes**  |Thales|Cristian|

## 4ª Interação – API
| Atividade                                      | Feature               | Autor    | Revisor  |
| ---------------------------------------------- | --------------------- | -------- | -------- |
| Tela e função de editar transação              | **US-Auth-06_editar** | Gabriel  | Cristian |
| Botão e função de excluir transação            | **US-Auth-06_excluir**| Vinicius | Gabriel  |
| Correções de problemas na página de metas      | **US-Auth-07_metas**  | Cristian | Thales   |
| Implementação de API externa (login, cadastro) | **US-Auth-08_google_auth**| Thales   | Vinicius |

## 5ª Interação – Perfil Usuário & Perfil Admin
| Atividade                                            | Feature                  | Autor    | Revisor  |
| ---------------------------------------------------- | ------------------------ | -------- | -------- |
| Implementação de Testes Unitários                    | **teste_unitario**       | Cristian | Gabriel  |
| Implementação de API interna (login, cadastro, etc.) | **RF008_google_auth**    | Thales   | Gabriel  |
| Tela e função de administradores                     | **RF019_adm**            | Gabriel  | Vinicius |
| Refatoração do código após novas features            | **RF006_editar_excluir** | Vinicius | Thales   |
| Refatoração do código após novas features            | **RF007_grafdashboard**  | Gabriel  | Cristian |
| Melhorias                                            | **RF007_metas**          | Thales   | Vinicius |