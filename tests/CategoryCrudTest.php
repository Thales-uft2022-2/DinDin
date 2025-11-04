<?php
session_start(); $_SESSION['user_id'] = 1;

require_once __DIR__ . '/../app/services/CategoryService.php';

$svc = new CategoryService();

// Criar
$r = $svc->create(1, ['name'=>'Teste Sprint','type'=>'entrada','color'=>'#11aa11']);
if ($r['error'] ?? false) exit("[TS-Test-02] FALHA ao criar\n");
$id = (int)$r['id'];

// Listar
$all = $svc->list(1,1,100);
if (!is_array($all) || count($all)==0) exit("[TS-Test-02] FALHA ao listar\n");

// Editar
$u = $svc->update(1,$id,['name'=>'Teste Sprint Editado','color'=>'#22bb22']);
if ($u['error'] ?? false) exit("[TS-Test-02] FALHA ao editar\n");

// Buscar
$one = $svc->get(1,$id);
if (!$one || $one['name']!=='Teste Sprint Editado') exit("[TS-Test-02] FALHA ao buscar\n");

// Deletar (lógico)
$d = $svc->delete(1,$id);
if ($d['error'] ?? false) exit("[TS-Test-02] FALHA ao deletar\n");

// Conferir remoção lógica
$gone = $svc->get(1,$id);
if ($gone!==null) exit("[TS-Test-02] FALHA: não foi removido logicamente\n");

echo "[TS-Test-02] OK: CRUD categorias passou\n";