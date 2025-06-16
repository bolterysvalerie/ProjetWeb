<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="create-post-container">    <div class="create-post-header">
        <h2>Créer un nouvel article</h2>
        <a href="index.php?url=blog" class="btn btn-secondary">← Retour au blog</a>
    </div>

    <form action="index.php?url=blog&action=create" method="POST" class="create-post-form">
        <div class="form-group">
            <label for="title">Titre de l'article * :</label>
            <input type="text" id="title" name="title" required maxlength="200"
                   value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
        </div>

        <div class="form-group">
            <label for="content">Contenu de l'article * :</label>
            <textarea id="content" name="content" required rows="15"><?= isset($_POST['content']) ? htmlspecialchars($_POST['content']) : '' ?></textarea>
        </div>        <div class="form-actions">
            <button type="submit" class="btn btn-success">Publier l'article</button>
            <a href="index.php?url=blog" class="btn btn-secondary">Annuler</a>
        </div>
    </form>

    <div class="form-info">
        <h4>Conseils pour rédiger un bon article :</h4>
        <ul>
            <li>Choisissez un titre accrocheur et descriptif</li>
            <li>Structurez votre contenu avec des paragraphes</li>
            <li>Relisez-vous avant de publier</li>
            <li>Les utilisateurs pourront commenter votre article</li>
        </ul>
    </div>
</div>

