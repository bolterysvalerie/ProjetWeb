<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="chat-container">    <div class="chat-header">
        <h2>Mini-Chat</h2>
        <p>Discutez en temps réel avec les autres membres connectés</p>
        <div class="scroll-indicator">
            <small class="text-muted"><i class="fas fa-arrows-alt-v"></i> Faites défiler pour voir tous les messages</small>
        </div>
        <div id="newMessagesIndicator" class="new-messages-indicator" style="display: none;">
            <small><i class="fas fa-envelope"></i> Nouveaux messages disponibles !</small>
        </div>
    </div>    <div id="loadMoreContainer" style="text-align: center; padding: 5px; display: none;">
        <button id="loadMoreMessages" class="btn btn-sm btn-light">
            <i class="fas fa-history"></i> Voir les messages précédents
        </button>
    </div>
    
    <div class="chat-messages" id="chatMessages">
        <?php if(empty($messages)): ?>
            <p class="no-messages">Aucun message pour le moment. Soyez le premier à écrire !</p>
        <?php else: ?>
            <?php foreach($messages as $message): ?>
                <div class="chat-message">
                    <div class="message-header">
                        <strong class="message-author"><?= htmlspecialchars($message['pseudo']) ?></strong>
                        <span class="message-time">
                            <?= date('H:i', strtotime($message['date_message'])) ?>
                        </span>
                    </div>
                    <div class="message-content">
                        <?= nl2br(htmlspecialchars($message['message'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div><div class="chat-form-container">
        <form action="index.php?url=chat&action=send" method="POST" class="chat-form" id="chatForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="pseudo">Pseudo :</label>
                    <input type="text" id="pseudo" name="pseudo" 
                           value="<?= htmlspecialchars($_SESSION['pseudo'] ?? $_SESSION['username']) ?>" 
                           maxlength="50" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="message">Message :</label>
                <textarea id="message" name="message" required 
                          placeholder="Tapez votre message..." 
                          maxlength="500" rows="3"></textarea>
                <small>Maximum 500 caractères</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Envoyer</button>
                <button type="button" id="refreshChat" class="btn btn-info">Actualiser</button>
            </div>
        </form>
    </div>

    <div class="chat-info">
        <h4>Règles du chat :</h4>
        <ul>
            <li>Restez respectueux envers les autres utilisateurs</li>
            <li>Les messages sont limités à 500 caractères</li>
            <li>Seuls les 10 derniers messages sont affichés</li>
            <li>Vous pouvez changer votre pseudo pour cette session</li>
        </ul>
    </div>
</div>

<script>
// Script pour l'actualisation automatique du chat
class ChatManager {
    constructor() {
        this.refreshInterval = null;
        this.init();
    }    init() {
        console.log("Initialisation du gestionnaire de chat...");
        
        // Garder une trace de tous les messages
        this.allMessages = [];
        this.currentVisibleCount = 5; // Nombre de messages initialement affichés
        
        // Afficher d'abord les messages qui ont été rendus par le serveur
        // puis actualiser immédiatement pour obtenir les derniers messages
        setTimeout(() => {
            console.log("Actualisation initiale des messages...");
            this.refreshMessages();
            
            // Puis démarrer l'actualisation automatique
            this.startAutoRefresh();
        }, 500);
        
        // Bouton d'actualisation manuelle
        document.getElementById('refreshChat').addEventListener('click', () => {
            console.log("Actualisation manuelle...");
            this.refreshMessages();
        });
        
        // Bouton pour voir les messages précédents
        document.getElementById('loadMoreMessages').addEventListener('click', () => {
            console.log("Chargement des messages précédents...");
            this.loadMoreMessages();
        });
        
        // Actualiser après envoi de message
        document.getElementById('chatForm').addEventListener('submit', (e) => {
            console.log("Formulaire soumis, actualisation programmée...");
            setTimeout(() => this.refreshMessages(), 1000);
        });
        
        // Scroll automatique vers le bas
        this.scrollToBottom();
        
        // Afficher un indicateur de défilement lorsque l'utilisateur survole la zone de chat
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.addEventListener('mouseover', () => {
            chatMessages.style.scrollbarWidth = 'auto';
            chatMessages.style.scrollbarColor = '#0d6efd #f8f9fa';
            chatMessages.classList.add('active-scroll');
        });
        
        chatMessages.addEventListener('mouseout', () => {
            chatMessages.classList.remove('active-scroll');
        });
          // Masquer l'indicateur de défilement après 15 secondes
        setTimeout(() => {
            const scrollIndicator = document.querySelector('.scroll-indicator');
            if (scrollIndicator) {
                scrollIndicator.classList.add('fade-out');
                setTimeout(() => {
                    scrollIndicator.style.display = 'none';
                }, 1500); // Animation plus longue pour un fondu progressif
            }
        }, 15000); // Temps plus long pour laisser l'utilisateur voir l'indicateur
    }
      startAutoRefresh() {
        console.log("Démarrage de l'actualisation automatique...");
        this.refreshInterval = setInterval(() => {
            this.refreshMessages();
        }, 5000); // Actualisation toutes les 5 secondes
    }    async refreshMessages() {
        try {
            console.log("Récupération des messages...");
            // Assurons-nous que nous utilisons l'URL correcte avec le paramètre de cache pour éviter les problèmes de mise en cache
            const timestamp = new Date().getTime();
            const response = await fetch(`index.php?url=chat&action=get_messages&_=${timestamp}`);
            
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            
            const messages = await response.json();
            console.log(`${messages.length} messages récupérés:`, messages);
            
            if (messages && Array.isArray(messages)) {
                // Sauvegarder tous les messages
                this.allMessages = messages;
                
                // Afficher le bouton "Voir les messages précédents" si nécessaire
                const loadMoreContainer = document.getElementById('loadMoreContainer');
                if (messages.length > this.currentVisibleCount) {
                    loadMoreContainer.style.display = 'block';
                } else {
                    loadMoreContainer.style.display = 'none';
                }
                
                // Vérifions s'il y a de nouveaux messages
                const currentCount = document.querySelectorAll('.chat-message').length;
                if (currentCount > 0 && messages.length > currentCount) {
                    // Afficher l'indicateur de nouveaux messages
                    const newMessagesIndicator = document.getElementById('newMessagesIndicator');
                    if (newMessagesIndicator) {
                        newMessagesIndicator.style.display = 'block';
                        // Masquer après 5 secondes
                        setTimeout(() => {
                            newMessagesIndicator.style.display = 'none';
                        }, 5000);
                    }
                }
                
                this.updateMessagesDisplay(messages);
            } else {
                console.error('Format de réponse invalide:', messages);
            }
        } catch (error) {
            console.error('Erreur lors de l\'actualisation:', error);
        }
    }
    
    loadMoreMessages() {
        // Augmenter le nombre de messages visibles
        this.currentVisibleCount = Math.min(this.currentVisibleCount + 5, this.allMessages.length);
        
        // Mettre à jour l'affichage avec plus de messages
        this.updateMessagesDisplay(this.allMessages, false);
        
        // Masquer le bouton si tous les messages sont affichés
        if (this.currentVisibleCount >= this.allMessages.length) {
            document.getElementById('loadMoreContainer').style.display = 'none';
        }
    }    updateMessagesDisplay(messages, scrollToBottom = true) {
        const container = document.getElementById('chatMessages');
        
        if (!messages || messages.length === 0) {
            console.log('Aucun message à afficher');
            container.innerHTML = '<p class="no-messages">Aucun message pour le moment. Soyez le premier à écrire !</p>';
            return;
        }
        
        console.log(`Mise à jour de l'affichage avec ${messages.length} messages`);
        
        // Vider complètement le conteneur avant d'ajouter les nouveaux messages
        container.innerHTML = '';
        
        // Obtenir le pseudo de l'utilisateur courant (pour styliser les messages différemment)
        const currentUserPseudo = document.getElementById('pseudo').value;
        
        // Calculer le nombre de messages à afficher en fonction de la visibilité actuelle
        const startIndex = messages.length > this.currentVisibleCount ? 
                          messages.length - this.currentVisibleCount : 0;
        const visibleMessages = messages.slice(startIndex);
        
        // S'assurer que les messages visibles sont affichés
        visibleMessages.forEach((message, index) => {
            console.log(`Traitement du message ${index + 1}/${visibleMessages.length}: ${message.pseudo} - ${message.message.substring(0, 20)}...`);
            
            if (!message.date_message) {
                console.error('Message sans date:', message);
                return;
            }
            
            const date = new Date(message.date_message);
            const time = date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            
            // Déterminer si c'est un message de l'utilisateur courant
            const isCurrentUser = message.pseudo === currentUserPseudo;
            
            // Créer l'élément de message
            const messageDiv = document.createElement('div');
            messageDiv.className = 'chat-message' + (isCurrentUser ? ' own' : ' other');
            messageDiv.innerHTML = `
                <div class="message-header">
                    <strong class="message-author">${this.escapeHtml(message.pseudo)}</strong>
                    <span class="message-time">${time}</span>
                </div>
                <div class="message-content">
                    ${this.escapeHtml(message.message).replace(/\n/g, '<br>')}
                </div>
            `;
            
            // Ajouter directement au conteneur
            container.appendChild(messageDiv);        });
        
        console.log('Affichage des messages mis à jour');
        
        // Ne faire défiler vers le bas que si demandé (par exemple, pas lors du chargement de messages plus anciens)
        if (scrollToBottom) {
            this.scrollToBottom();
        }
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    scrollToBottom() {
        const container = document.getElementById('chatMessages');
        container.scrollTop = container.scrollHeight;
    }
}

// Initialiser le gestionnaire de chat
document.addEventListener('DOMContentLoaded', () => {
    new ChatManager();
});
</script>

