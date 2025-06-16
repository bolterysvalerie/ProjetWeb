/*
 * Base de données E-Commerce - Version Complète et Unifiée
 * Mise à jour : 15 juin 2025
 * 
 * Ce script contient :
 * - Structure complète des tables avec toutes les améliorations
 * - Colonne statut pour les commandes (en_attente, confirmee, expediee, livree, annulee)
 * - Colonne product_name dans order_details pour conserver l'historique
 * - Utilisateur admin par défaut (admin/password)  
 * - Utilisateurs de test supplémentaires
 * - Produits de démonstration dans 3 catégories
 * - Articles de blog et commentaires
 * - Messages de chat d'exemple
 * - Commandes et historique de démonstration avec noms de produits
 * - Données de connexion pour statistiques
 * - Scripts de mise à jour pour bases de données existantes
 */

CREATE DATABASE IF NOT EXISTS e_commerce_web CHARACTER SET utf8 COLLATE utf8_general_ci;
USE e_commerce_web;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    adresse TEXT NOT NULL,
    code_postal VARCHAR(10) NOT NULL,
    date_naissance DATE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    pseudo VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    image_profil VARCHAR(255) DEFAULT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    is_blocked TINYINT(1) DEFAULT 0,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table des connexions pour le suivi
CREATE TABLE IF NOT EXISTS user_connections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date_connexion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des catégories de produits
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- Table des produits
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    category_id INT NOT NULL,
    stock INT DEFAULT 100,
    image VARCHAR(255),
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Table des commandes
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'confirmee', 'expediee', 'livree', 'annulee') DEFAULT 'en_attente',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des détails de commande
CREATE TABLE IF NOT EXISTS order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) DEFAULT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Table des billets de blog
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    contenu TEXT NOT NULL,
    author_id INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des commentaires
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table du mini-chat
CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    pseudo VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    date_message DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insertion des catégories
INSERT INTO categories (nom, description) VALUES 
('informatique', 'Matériel informatique et accessoires'),
('livre', 'Livres et publications'),
('hi-fi', 'Matériel audio et hi-fi');

-- Insertion de produits d'exemple
INSERT INTO products (nom, description, prix, category_id, stock) VALUES 
-- Informatique
('Ordinateur portable Dell', 'PC portable 15 pouces, 8GB RAM, SSD 256GB', 799.99, 1, 100),
('Clavier mécanique', 'Clavier gaming RGB mécanique', 89.99, 1, 100),
('Souris gaming', 'Souris optique haute précision', 49.99, 1, 100),
('Écran 24 pouces', 'Moniteur Full HD IPS', 199.99, 1, 100),
('Disque dur externe 1TB', 'Stockage externe portable', 79.99, 1, 100),
('Webcam HD', 'Caméra Full HD avec micro intégré', 59.99, 1, 100),

-- Livres
('Le Petit Prince', 'Roman classique d\'Antoine de Saint-Exupéry', 12.99, 2, 100),
('JavaScript pour les nuls', 'Guide pratique de programmation JavaScript', 29.99, 2, 100),
('1984', 'Roman dystopique de George Orwell', 15.99, 2, 98),
('Clean Code', 'Guide des bonnes pratiques de développement', 45.99, 2, 100),
('Harry Potter à l\'école des sorciers', 'Premier tome de la saga', 18.99, 2, 100),

-- Hi-Fi
('Casque audio Bluetooth', 'Casque sans fil avec réduction de bruit', 149.99, 3, 100),
('Enceinte portable', 'Haut-parleur Bluetooth étanche', 79.99, 3, 100),
('Amplificateur', 'Ampli stéréo 2x50W', 299.99, 3, 100),
('Platine vinyle', 'Tourne-disque professionnel', 399.99, 3, 100),
('Micro studio', 'Microphone de studio USB', 189.99, 3, 100),
('Écouteurs intra-auriculaires', 'Écouteurs haute fidélité', 99.99, 3, 100),

-- Produits supplémentaires
('Ordinateur portable Dell Inspiron', 'Ordinateur portable Dell Inspiron 15.6" avec processeur Intel Core i5, 8GB RAM, 256GB SSD', 699.99, 1, 10),
('Souris gaming Logitech', 'Souris gaming haute précision avec capteur optique 16000 DPI', 79.99, 1, 25),
('Clavier mécanique RGB', 'Clavier mécanique RGB avec switches Cherry MX Blue', 129.99, 1, 15),
('Le Petit Prince (Édition collector)', 'Roman classique d\'Antoine de Saint-Exupéry - Édition collector', 9.99, 2, 50),
('Guide PHP Avancé', 'Manuel complet pour maîtriser PHP et le développement web', 45.99, 2, 20),
('Casque audio Sony', 'Casque audio sans fil avec réduction de bruit active', 249.99, 3, 12),
('Enceinte Bluetooth JBL', 'Enceinte portable étanche avec autonomie 20h', 89.99, 3, 18),
('Spring Boot', 'Guide complet du framework Spring Boot', 49.99, 2, 5);

-- Création d'un utilisateur administrateur par défaut
INSERT INTO users (nom, prenom, adresse, code_postal, date_naissance, email, pseudo, password, is_admin) 
VALUES ('Admin', 'Site', '123 Rue Admin', '1000', '1990-01-01', 'admin@site.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
-- Mot de passe : password

-- Création d'utilisateurs supplémentaires
INSERT INTO users (nom, prenom, adresse, code_postal, date_naissance, email, pseudo, password, is_admin) VALUES 
('Test', 'User', 'Adresse test', '1400', '2002-07-16', 'test2@gmail.com', 'test2', '$2y$10$238CNtoGBhvcOSUjq5rXbuwgQyV6DnCZjisPCuKTz9SrP./TeZGYK', 0),
('Valerie', 'Bolterys', 'Adresse complète', '1400', '1988-05-19', 'valerie@gmail.com', 'test3', '$2y$10$DYz6vHpiFRnm4gipxTZ6O.1SbOzBuj5G18JdOQRt8OypENF3hKFQS', 0);

-- Insertion d'articles de blog de démonstration
INSERT INTO blog_posts (titre, contenu, author_id) VALUES 
('Guide d\'achat : Comment choisir le parfait smartphone', 'Avec tant d\'options disponibles, choisir un nouveau smartphone peut être difficile. Notre guide complet vous aide à évaluer les critères importants : appareil photo, autonomie, performance et budget pour faire le bon choix.', 1),
('Les avantages du shopping en ligne : confort et sécurité', 'Le commerce électronique a révolutionné notre façon de faire du shopping. Explorez les nombreux avantages du shopping en ligne : gain de temps, comparaison facile des prix, livraison à domicile et mesures de sécurité renforcées.', 1);

-- Insertion de commentaires de démonstration
INSERT INTO comments (post_id, user_id, contenu) VALUES 
(1, 1, 'Guide très utile! L\'aspect appareil photo est effectivement crucial de nos jours pour choisir un smartphone.'),
(2, 1, 'Le shopping en ligne est devenu indispensable. La sécurité des paiements s\'est vraiment améliorée ces dernières années.');

-- Insertion de messages de chat de démonstration
INSERT INTO chat_messages (user_id, pseudo, message) VALUES 
(1, 'admin', 'Bienvenue dans le mini-chat !'),
(1, 'admin', 'N\'hésitez pas à poser vos questions ici.');

-- Insertion de commandes de démonstration
INSERT INTO orders (user_id, total) VALUES 
(1, 389.99),
(1, 497.95),
(2, 699.99);

-- Insertion de détails de commande de démonstration
INSERT INTO order_details (order_id, product_id, product_name, quantite, prix_unitaire) VALUES 
(1, 2, 'Laptop Gaming Pro', 2, 89.99),
(1, 4, 'Smartphone Ultra', 1, 199.99),
(2, 9, 'T-shirt Vintage', 3, 15.99),
(2, 14, 'Casque Audio Premium', 1, 299.99),
(2, 12, 'Montre Sport', 1, 149.99),
(3, 18, 'Ordinateur Gamer', 1, 699.99);

-- Insertion de connexions utilisateur pour les statistiques
INSERT INTO user_connections (user_id) VALUES 
(1), (1), (1), (2), (2), (3), (3);

-- ==============================================
-- MISES À JOUR POUR BASES DE DONNÉES EXISTANTES
-- ==============================================

-- Ajouter la colonne statut à la table orders si elle n'existe pas
-- (Cette modification est déjà incluse dans la structure de base ci-dessus)
-- ALTER TABLE orders 
-- ADD COLUMN IF NOT EXISTS statut ENUM('en_attente', 'confirmee', 'expediee', 'livree', 'annulee') 
-- DEFAULT 'en_attente' AFTER date_commande;

-- Ajouter la colonne product_name à la table order_details si elle n'existe pas
-- (Cette modification est déjà incluse dans la structure de base ci-dessus)
-- ALTER TABLE order_details 
-- ADD COLUMN IF NOT EXISTS product_name VARCHAR(255) DEFAULT NULL AFTER product_id;

-- Mettre à jour les noms de produits dans les commandes existantes
-- Cette requête peut être exécutée en toute sécurité même si les données sont déjà correctes
UPDATE order_details od 
INNER JOIN products p ON od.product_id = p.id 
SET od.product_name = p.nom 
WHERE od.product_name IS NULL OR od.product_name = '';

-- Gérer les produits supprimés
UPDATE order_details 
SET product_name = CONCAT('Produit supprimé (ID: ', product_id, ')') 
WHERE (product_name IS NULL OR product_name = '') 
AND product_id NOT IN (SELECT id FROM products);

-- Mettre à jour les commandes existantes pour avoir un statut par défaut
UPDATE orders SET statut = 'en_attente' WHERE statut IS NULL;

-- ==============================================
-- FIN DU SCRIPT DE BASE DE DONNÉES
-- ==============================================
