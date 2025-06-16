<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Inscription</h2>
        
        <form action="index.php?url=user&action=register" method="POST" enctype="multipart/form-data" class="auth-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom * :</label>
                    <input type="text" id="nom" name="nom" required 
                           value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="prenom">Prénom * :</label>
                    <input type="text" id="prenom" name="prenom" required 
                           value="<?= isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : '' ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="adresse">Adresse * :</label>
                <textarea id="adresse" name="adresse" required rows="3"><?= isset($_POST['adresse']) ? htmlspecialchars($_POST['adresse']) : '' ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="code_postal">Code postal * :</label>
                    <input type="text" id="code_postal" name="code_postal" required 
                           value="<?= isset($_POST['code_postal']) ? htmlspecialchars($_POST['code_postal']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="date_naissance">Date de naissance * :</label>
                    <input type="date" id="date_naissance" name="date_naissance" required 
                           value="<?= isset($_POST['date_naissance']) ? htmlspecialchars($_POST['date_naissance']) : '' ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email * :</label>
                <input type="email" id="email" name="email" required 
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="pseudo">Nom d'utilisateur * :</label>
                    <input type="text" id="pseudo" name="pseudo" required 
                           value="<?= isset($_POST['pseudo']) ? htmlspecialchars($_POST['pseudo']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe * :</label>
                    <input type="password" id="password" name="password" required minlength="6">
                    <small>Au moins 6 caractères</small>
                </div>
            </div>
            
            <div class="form-group">
                <label for="image_profil">Image de profil (optionnel) :</label>
                <input type="file" id="image_profil" name="image_profil" accept=".jpg,.jpeg,.gif,.png">
                <small>Formats acceptés : JPG, GIF, PNG (max 2MB)</small>
            </div>
            
            <button type="submit" class="btn btn-success btn-full">S'inscrire</button>
        </form>
        
        <div class="auth-links">
            <p>Déjà un compte ? <a href="index.php?page=login">Connectez-vous</a></p>
            <p><a href="index.php">Retour à l'accueil</a></p>
        </div>
        
        <div class="form-info">
            <p><small>* Champs obligatoires</small></p>
        </div>
    </div>
</div>

