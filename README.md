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
**Como** visitante  
**Quero** criar uma conta informando e-mail e senha  
**Para** acessar o sistema com meus próprios dados  

**Critérios de Aceite**
- Dado que estou na página de registro  
  Quando preencho e-mail válido e senha (mín. 8 caracteres) e envio  
  Então minha conta é criada e sou direcionado(a) para a página inicial logado(a).  
- Dado que já existe um usuário com esse e-mail  
  Quando tento registrar novamente  
  Então vejo uma mensagem “e-mail já cadastrado”.  

---

### US-Auth-02 — Login
**Como** usuário cadastrado  
**Quero** entrar com e-mail e senha  
**Para** acessar minhas funcionalidades  

**Critérios de Aceite**
- Dado que informo credenciais válidas  
  Quando envio o formulário  
  Então sou autenticado(a) e vejo a página inicial.  
- Dado que informo credenciais inválidas  
  Quando envio o formulário  
  Então vejo uma mensagem clara de erro sem revelar detalhes de segurança.  

---

### US-Auth-03 — Logout
**Como** usuário autenticado  
**Quero** sair da minha sessão  
**Para** encerrar o acesso em dispositivos compartilhados  

**Critérios de Aceite**
- Dado que estou logado(a)  
  Quando clico em “Sair”  
  Então minha sessão é finalizada e sou enviado(a) para a página de login.  

---

### US-Auth-04 — Lembrar sessão *(opcional)*
**Como** usuário  
**Quero** marcar “Lembrar-me” no login  
**Para** permanecer logado(a) entre visitas  

**Critérios de Aceite**
- Dado que marco “Lembrar-me”  
  Quando fecho e reabro o navegador  
  Então continuo autenticado(a) até que eu faça logout.  

---

### US-Auth-05 — Recuperar Senha *(opcional)*
**Como** usuário  
**Quero** receber um link/código para redefinir a senha  
**Para** recuperar o acesso quando eu esquecer  

**Critérios de Aceite**
- Dado que informo um e-mail cadastrado  
  Quando solicito “Esqueci minha senha”  
  Então recebo instruções claras de redefinição.  
- Dado que informo e-mail não cadastrado  
  Quando solicito redefinição  
  Então vejo mensagem genérica (“Se existir, enviaremos instruções”), sem vazar existência da conta.  

---

# 📌 DIVISÃO DAS TAREFAS

## 1° Sprint - Gestão de Transações

| Atividade                                  | Feature                 | Autor     | Revisor   |
|--------------------------------------------|-------------------------|-----------|-----------|
| Tela e função de cadastro de transação     | **US-Tx-01_cadastrar**  | Thales    | Caio      |
| Tela e função de listar transações         | **US-Tx-02_listar**     | Gabriel      | Cristian  |
| Tela e função de filtrar/buscar transações | **US-Tx-03_filtrar**    | Caio  | Vinicius  |
| Tela e função de editar transação          | **US-Tx-04_editar**     | Vinicius  | Gabriel   |
| Botão e função de excluir transação        | **US-Tx-05_excluir**    | Cristian   | Thales    |

---

## 2° Sprint - Usuário & Autenticação

| Atividade                                  | Feature                 | Autor     | Revisor   |
|--------------------------------------------|-------------------------|-----------|-----------|
| Tela e função de registro de usuário       | **US-Auth-01_registro** | Gabriel   | Vinicius  |
| Tela e função de login                     | **US-Auth-02_login**    | Vinicius  | Cristian  |
| Tela e função de logout                    | **US-Auth-03_logout**   | Cristian  | Caio      |
| Função “Lembrar-me” (opcional)             | **US-Auth-04_lembrar**  | Caio      | Thales    |
| Função de recuperação de senha (opcional)  | **US-Auth-05_recuperar**| Thales    | Gabriel   |
