# 🛒 Projet E-Commerce PHP

Un site e-commerce complet développé en PHP avec une interface moderne et responsive.

## 🚀 Fonctionnalités

### 👥 Gestion des utilisateurs
- ✅ Inscription et connexion
- ✅ Profils utilisateur avec avatars
- ✅ Rôles (Admin/Client)
- ✅ Gestion des sessions

### 🛍️ Boutique en ligne
- ✅ Catalogue de produits avec images
- ✅ Panier d'achat dynamique
- ✅ Gestion des commandes
- ✅ Historique des achats
- ✅ Gestion des stocks

### 👨‍💼 Panel d'administration
- ✅ Gestion des produits (CRUD)
- ✅ Gestion des utilisateurs
- ✅ Suivi des commandes
- ✅ Statistiques détaillées
- ✅ Tableau de bord interactif

### 📝 Blog intégré
- ✅ Articles de blog
- ✅ Système de commentaires
- ✅ Interface de rédaction

### 💬 Chat en temps réel
- ✅ Messagerie intégrée
- ✅ Interface moderne

## 🛠️ Technologies utilisées

- **Backend :** PHP 8.2+ avec PDO
- **Frontend :** HTML5, CSS3, JavaScript, Bootstrap 5
- **Base de données :** MySQL
- **Serveur :** Apache (WAMP/XAMPP)

## 📦 Installation

### Prérequis
- WAMP, XAMPP ou serveur Apache/PHP/MySQL
- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone https://github.com/votre-username/e_commerce_projet_web.git
cd e_commerce_projet_web
```

2. **Configurer la base de données**
```bash
# Importer le fichier SQL
mysql -u root -p < database.sql
```

Ou utiliser le script PHP :
```
http://localhost/e_commerce_projet_web/setup_database.php
```

3. **Configurer la connexion**
Vérifier les paramètres dans `config/database.php`

4. **Lancer l'application**
```
http://localhost/e_commerce_projet_web/
```

## 👤 Comptes de test

### Administrateur
- **Email :** admin@example.com
- **Mot de passe :** password

### Utilisateur test
- **Email :** user@example.com
- **Mot de passe :** password

## 📁 Structure du projet

```
├── assets/                 # Ressources statiques
│   ├── css/               # Styles CSS
│   ├── js/                # Scripts JavaScript
│   └── images/            # Images
├── config/                # Configuration
├── controllers/           # Contrôleurs MVC
├── models/                # Modèles de données
├── views/                 # Vues et templates
├── uploads/               # Fichiers uploadés
├── database.sql           # Script de base de données
└── index.php              # Point d'entrée
```

## 🎨 Fonctionnalités avancées

- 🎯 **Design responsive** avec CSS Grid et Flexbox
- 🌟 **Effets glassmorphism** pour une interface moderne
- 📊 **Statistiques en temps réel** pour les administrateurs
- 🔐 **Sécurité renforcée** avec protection XSS et CSRF
- 🖼️ **Gestion d'images** avec fallback automatique
- 📱 **Interface mobile-friendly**

## 🔧 Développement

### Ajout de nouvelles fonctionnalités
1. Créer le modèle dans `models/`
2. Créer le contrôleur dans `controllers/`
3. Créer la vue dans `views/`
4. Ajouter les routes dans `index.php`

### Styles CSS
Les styles sont organisés par composants dans `assets/css/style.css`

## 🐛 Dépannage

### Problèmes courants
- **Erreur de connexion MySQL :** Vérifier WAMP/XAMPP
- **Images manquantes :** Vérifier les permissions du dossier `uploads/`
- **Erreurs PHP :** Vérifier la version PHP (8.0+ requis)

## 👨‍💻 Développeurs

Développé par **Adrian et Valérie**

## 📄 Licence

Ce projet est sous licence MIT.
