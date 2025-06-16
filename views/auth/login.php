<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Connexion</h2>
        
        <form action="index.php?url=user&action=login" method="POST" class="auth-form">
            <div class="form-group">
                <label for="pseudo">Nom d'utilisateur :</label>
                <input type="text" id="pseudo" name="pseudo" required 
                       value="<?= isset($_POST['pseudo']) ? htmlspecialchars($_POST['pseudo']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full">Se connecter</button>
        </form>
        
        <div class="auth-links">
            <p>Pas encore de compte ? <a href="index.php?page=register">Inscrivez-vous</a></p>
            <p><a href="index.php">Retour à l'accueil</a></p>
        </div>
        
        <div class="demo-info">
            <h4>Compte de démonstration :</h4>
            <p><strong>Admin :</strong> admin / password</p>
        </div>    </div>
</div>
