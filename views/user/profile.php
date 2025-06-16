<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="profile-container">
    <div class="profile-header">
        <h2>Mon Profil</h2>
        <?php if($user['image_profil']): ?>
            <img src="uploads/profiles/<?= htmlspecialchars($user['image_profil']) ?>" 
                 alt="Photo de profil" class="profile-photo">
        <?php endif; ?>
    </div>

    <div class="profile-content">
        <div class="profile-info">
            <h3>Informations personnelles</h3>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?>
                </div>
                <div class="info-item">
                    <strong>Prénom :</strong> <?= htmlspecialchars($user['prenom']) ?>
                </div>
                <div class="info-item">
                    <strong>Pseudo :</strong> <?= htmlspecialchars($user['pseudo']) ?>
                </div>
                <div class="info-item">
                    <strong>Date de naissance :</strong> <?= date('d/m/Y', strtotime($user['date_naissance'])) ?>
                </div>
                <div class="info-item">
                    <strong>Date d'inscription :</strong> <?= date('d/m/Y', strtotime($user['date_inscription'])) ?>
                </div>
            </div>
        </div>

        <div class="profile-edit">
            <h3>Modifier mes informations</h3>
            <form action="index.php?url=user&action=profile" method="POST" class="profile-form">
                <div class="form-group">
                    <label for="adresse">Adresse :</label>
                    <textarea id="adresse" name="adresse" required rows="3"><?= htmlspecialchars($user['adresse']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="code_postal">Code postal :</label>
                    <input type="text" id="code_postal" name="code_postal" required 
                           value="<?= htmlspecialchars($user['code_postal']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= htmlspecialchars($user['email']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Nouveau mot de passe (laisser vide pour ne pas changer) :</label>
                    <input type="password" id="password" name="password" minlength="6">
                    <small>Au moins 6 caractères</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </form>
        </div>
    </div>
    
    <div class="profile-actions">
        <a href="index.php?page=orders" class="btn btn-info">Mes commandes</a>
        <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
    </div>
</div>

