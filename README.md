# 🛠️ Système de Gestion des Projets et des Tâches

## 📌 Description

Ce projet est une **application web collaborative** destinée à la **gestion des tâches et des projets en équipe**.  
Elle offre une interface intuitive pour organiser, répartir et suivre les tâches en utilisant un tableau **Kanban interactif**.

Le projet contient également le **PPT de présentation** complet du système, incluant la description, la conception et les fonctionnalités.

## 🏗️ Conception

Pour garantir un **code propre, modulaire et évolutif**, plusieurs **design patterns** ont été intégrés à l’architecture du système.  
Le projet suit une architecture **MVC (Modèle-Vue-Contrôleur)** pour une meilleure séparation des responsabilités.

Les design patterns utilisés sont :  
- `Command`  
- `Observer`  
- `Composite`  
- `State`  
- `Strategy`  
- `Factory`

Ces patterns permettent notamment :  
- Gestion des actions sur les tâches avec possibilité d’annulation (`Command`)  
- Notifications automatiques aux membres en cas de mise à jour (`Observer`)  
- Structure hiérarchique des tâches et sous-tâches (`Composite`)  
- Gestion des différents statuts d’une tâche (`State`)  
- Méthodes de tri personnalisables des tâches (`Strategy`)  
- Création flexible de différents types de tâches (`Factory`)

## 🔧 Fonctionnalités principales

### 👤 Gestion des utilisateurs
- Authentification et gestion des sessions  
- Rôles : **Chef de Projet / Admin** et **Membre**

### 📁 Espace Projets
- Création, modification et suppression de projets  
- Invitation de membres à collaborer

### ✅ Espace Tâches (Kanban)
- Création, modification, suppression et assignation de tâches  
- Support des sous-tâches  
- Tri des tâches par priorité ou date limite  
- Changement de statut et déplacement dans le Kanban  
- Consultation des détails d’une tâche  
- Système de **notifications en temps réel**

## 🧱 Technologies utilisées
- **Langage** : PHP (OOP)  
- **Base de données** : MySQL  
- **Front-end** : HTML, CSS, Bootstrap, JavaScript  
- **Architecture** : MVC (Modèle-Vue-Contrôleur)


🙋‍♀️ Réalisé par  
Chaymae El Fahssi  
Étudiante en 4ᵉ année Génie Informatique – ENSA Tétouan
