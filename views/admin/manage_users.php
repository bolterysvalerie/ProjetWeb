<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users"></i> Gestion des utilisateurs</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php?url=admin">Admin</a></li>
                        <li class="breadcrumb-item active">Utilisateurs</li>
                    </ol>
                </nav>
            </div>

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Total Utilisateurs</h5>
                                    <h3><?= count($users) ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Utilisateurs Actifs</h5>
                                    <h3><?= count(array_filter($users, function($u) { return !$u['blocked']; })) ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Bloqués</h5>
                                    <h3><?= count(array_filter($users, function($u) { return $u['blocked']; })) ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-lock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Admins</h5>
                                    <h3><?= count(array_filter($users, function($u) { return $u['role'] === 'admin'; })) ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-shield fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des utilisateurs -->
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Liste des utilisateurs</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Avatar</th>
                                    <th>Utilisateur</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Inscription</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($users as $user): ?>
                                <tr class="<?= $user['blocked'] ? 'table-warning' : '' ?>">
                                    <td><?= $user['id'] ?></td>
                                    <td>
                                        <img src="<?= $user['avatar'] ?? 'assets/images/default-avatar.png' ?>" 
                                             alt="Avatar" class="rounded-circle" width="40" height="40">
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($user['username']) ?></strong>
                                        <?php if($user['role'] === 'admin'): ?>
                                            <span class="badge bg-primary ms-1">Admin</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $user['role'] === 'admin' ? 'primary' : 'secondary' ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($user['blocked']): ?>
                                            <span class="badge bg-danger">
                                                <i class="fas fa-ban"></i> Bloqué
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> Actif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">                                            <a href="index.php?url=admin&action=user_profile&id=<?= $user['id'] ?>" 
                                               class="btn btn-outline-info" title="Voir profil">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if($user['role'] !== 'admin'): ?>
                                                <form method="POST" action="index.php?page=toggle_user_block" 
                                                      style="display: inline;">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <input type="hidden" name="block" value="<?= $user['blocked'] ? '0' : '1' ?>">
                                                    <button type="submit" 
                                                            class="btn btn-outline-<?= $user['blocked'] ? 'success' : 'warning' ?>"
                                                            title="<?= $user['blocked'] ? 'Débloquer' : 'Bloquer' ?>"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir <?= $user['blocked'] ? 'débloquer' : 'bloquer' ?> cet utilisateur ?')">
                                                        <i class="fas fa-<?= $user['blocked'] ? 'unlock' : 'lock' ?>"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table th {
    border-top: none;
    font-weight: 600;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}

.badge {
    font-size: 0.75em;
}
</style>
