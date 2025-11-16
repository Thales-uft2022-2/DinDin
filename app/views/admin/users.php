<?php require __DIR__ . '/../_header.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4">Admin - Listagem de Usuários</h2>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Permissão</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= $u['name'] ?></td>
                <td><?= $u['email'] ?></td>
                <td><?= $u['role'] ?></td>
                <td><?= $u['status'] ?></td>
                <td>
                    <form action="/admin/update" method="POST" class="d-flex gap-2">

                        <input type="hidden" name="id" value="<?= $u['id'] ?>">

                        <select name="role" class="form-control">
                            <option value="user" <?= $u['role'] == 'user' ? 'selected' : '' ?>>Usuário</option>
                            <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>

                        <select name="status" class="form-control">
                            <option value="active" <?= $u['status'] == 'active' ? 'selected' : '' ?>>Ativo</option>
                            <option value="blocked" <?= $u['status'] == 'blocked' ? 'selected' : '' ?>>Bloqueado</option>
                        </select>

                        <button class="btn btn-primary">Salvar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>

<?php require __DIR__ . '/../_footer.php'; ?>