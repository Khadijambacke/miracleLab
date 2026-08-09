# Analyse d'Ingénierie Inverse & Architecture Backend Exhaustive · The Miracle Lab

Ce document contient l'analyse d'ingénierie inverse complète et détaillée du code source frontend (`index.html.bak`, `calculator.js`, `shared.js`). L'objectif est de transférer **100% des données métier et des règles de calcul** depuis le code JavaScript vers une base de données **MySQL** gérée par **Laravel 13**.

---

## 1. Identification Exhaustive des Entités et Tables Principales

Toutes les structures actuellement codées en dur dans le JavaScript sont extraites et transformées en entités relationnelles :

1. **`utilisateurs` (`users`)** : Utilisateurs du système (clients et administrateurs).
2. **`abonnements` (`subscriptions`)** : Historique des paiements de licence (15 000 FCFA).
3. **`categories_ingredients` (`ingredient_categories`)** : Familles d'ingrédients (ex: Tensioactifs, Humectants, Émulsifiants, Macérats).
4. **`ingredients` (`ingredients`)** : Catalogue complet des matières premières (système et personnalisées).
5. **`fiches_techniques` (`ingredient_sheets`)** : Données scientifiques associées aux ingrédients (INCI, solubilité, température, pH optimal).
6. **`proprietes_ingredients` (`ingredient_properties`)** : Bienfaits et propriétés des ingrédients.
7. **`types_produits` (`formula_types`)** : Squelettes de produits (Leave-in, Shampoing, Masque, Sérum, Crème).
8. **`cibles_ph` (`ph_targets`)** : Plages de pH cibles par type de produit.
9. **`phases_formule` (`formula_phases`)** : Phases de formulation (Aqueuse, Huileuse, Refroidissement, Phase A, B, C...).
10. **`formules` (`formulas`)** : Recettes cosmétiques créées par les utilisateurs.
11. **`formule_ingredients` (`formula_ingredients`)** : Composition détaillée des formules (ingrédient, %, masse calculée, coût/kg).
12. **`regles_compatibilite` (`compatibility_rules`)** : Règles d'incompatibilité chimique (ex: Cationique + Anionique).
13. **`messages` (`chat_messages`)** : Discussions du support technique en direct.
14. **`historique_activites` (`activities`)** : Journal d'audit pour le système Undo/Redo et le versioning.

---

## 2. Définition Détaillée de Tous les Champs par Table

### 2.1. Table `utilisateurs`
* `id` : BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `nom_complet` : VARCHAR(255) — Nom et prénom
* `email` : VARCHAR(255) UNIQUE — Email d'authentification
* `telephone` : VARCHAR(50) NULLABLE — Contact
* `mot_de_passe` : VARCHAR(255) — Hash du mot de passe
* `role` : ENUM('CLIENT', 'ADMIN') DEFAULT 'CLIENT'
* `statut_abonnement` : ENUM('ACTIF', 'INACTIF', 'EXPIRE') DEFAULT 'INACTIF'
* `remember_token` : VARCHAR(100) NULLABLE
* `created_at`, `updated_at`, `deleted_at` : TIMESTAMPS (avec SoftDeletes)

### 2.2. Table `abonnements`
* `id` : BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `utilisateur_id` : BIGINT UNSIGNED (FK -> `utilisateurs.id` ON DELETE CASCADE)
* `montant` : DECIMAL(10,2) DEFAULT 15000.00
* `devise` : VARCHAR(10) DEFAULT 'FCFA'
* `methode_paiement` : ENUM('WAVE', 'ORANGE_MONEY', 'FREE_MONEY', 'CARD')
* `reference_transaction` : VARCHAR(255) UNIQUE
* `statut` : ENUM('EN_ATTENTE', 'SUCCES', 'ECHEC') DEFAULT 'EN_ATTENTE'
* `date_debut`, `date_fin` : TIMESTAMP NULLABLE
* `created_at`, `updated_at` : TIMESTAMPS

### 2.3. Table `categories_ingredients`
* `id` : BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `nom` : VARCHAR(100) — Nom de la catégorie (ex: Tensioactifs, Humectants)
* `description` : TEXT NULLABLE
* `created_at`, `updated_at` : TIMESTAMPS

### 2.4. Table `ingredients`
* `id` : BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `utilisateur_id` : BIGINT UNSIGNED NULLABLE (FK -> `utilisateurs.id` ON DELETE SET NULL) — NULL si ingrédient système
* `categorie_id` : BIGINT UNSIGNED NULLABLE (FK -> `categories_ingredients.id` ON DELETE SET NULL)
* `nom` : VARCHAR(255) — Nom commercial
* `phase_defaut` : ENUM('AQUEUSE', 'HUILEUSE', 'REFROIDISSEMENT')
* `description_courte` : TEXT NULLABLE — Note explicative
* `pourcentage_min` : DECIMAL(5,2) NULLABLE — Dosage min recommandé
* `pourcentage_max` : DECIMAL(5,2) NULLABLE — Dosage max recommandé (limite de sécurité)
* `impact_ph` : DECIMAL(3,1) DEFAULT 0.0 — Modificateur empirique de pH (+1.5, -7.0, etc.)
* `est_personnalise` : BOOLEAN DEFAULT FALSE
* `created_at`, `updated_at`, `deleted_at` : TIMESTAMPS

### 2.5. Table `fiches_techniques`
* `id` : BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `ingredient_id` : BIGINT UNSIGNED UNIQUE (FK -> `ingredients.id` ON DELETE CASCADE)
* `nom_inci` : VARCHAR(255) — Nomenclature INCI
* `categorie_fonctionnelle` : VARCHAR(150) — Rôle chimique
* `solubilite` : VARCHAR(100) — Hydrosoluble, Liposoluble, etc.
* `temperature_incorporation` : VARCHAR(100) — Ex: `< 40°C`, `75°C`
* `ph_optimal_min`, `ph_optimal_max` : DECIMAL(3,1) NULLABLE
* `precautions` : TEXT NULLABLE — Mises en garde
* `conseils_formulateur` : TEXT NULLABLE — Astuces d'utilisation
* `created_at`, `updated_at` : TIMESTAMPS

### 2.6. Table `proprietes_ingredients`
* `id` : BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `ingredient_id` : BIGINT UNSIGNED (FK -> `ingredients.id` ON DELETE CASCADE)
* `libelle` : VARCHAR(255) — Bienfait (ex: "Fortifiant capillaire", "Hydratant profond")
* `created_at` : TIMESTAMP

### 2.7. Table `types_produits`
* `id` : BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `utilisateur_id` : BIGINT UNSIGNED NULLABLE (FK -> `utilisateurs.id` ON DELETE SET NULL)
* `code` : VARCHAR(100) UNIQUE — Ex: `LEAVE_IN`, `SHAMPOO`, `CREME_VISAGE`
* `libelle` : VARCHAR(255) — Nom du squelette
* `categorie_soin` : ENUM('haircare', 'skincare')
* `contient_eau` : BOOLEAN DEFAULT TRUE — Indique si le produit nécessite le calcul auto de l'eau
* `created_at`, `updated_at` : TIMESTAMPS

### 2.8. Table `cibles_ph`
* `id` : BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `type_produit_id` : BIGINT UNSIGNED UNIQUE (FK -> `types_produits.id` ON DELETE CASCADE)
* `ph_min` : DECIMAL(3,1) — Borne minimale optimale
* `ph_max` : DECIMAL(3,1) — Borne maximale optimale
* `created_at`, `updated_at` : TIMESTAMPS

### 2.9. Table `squelettes_composition`
* `id` : BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `type_produit_id` : BIGINT UNSIGNED (FK -> `types_produits.id` ON DELETE CASCADE)
* `ingredient_id` : BIGINT UNSIGNED (FK -> `ingredients.id` ON DELETE CASCADE)
* `phase` : VARCHAR(50) — Phase recommandée
* `pourcentage_defaut` : DECIMAL(5,2) — Proportion par défaut dans le squelette
* `ordre` : INT DEFAULT 0
* `created_at`, `updated_at` : TIMESTAMPS

### 2.10. Table `formules`
* `id` : BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `utilisateur_id` : BIGINT UNSIGNED (FK -> `utilisateurs.id` ON DELETE CASCADE)
* `type_produit_id` : BIGINT UNSIGNED NULLABLE (FK -> `types_produits.id` ON DELETE SET NULL)
* `nom` : VARCHAR(255) — Intitulé de la recette
* `categorie` : ENUM('haircare', 'skincare')
* `poids_lot_grammes` : DECIMAL(8,2) DEFAULT 1000.00 — Taille de fabrication
* `notes` : TEXT NULLABLE — Procédure de fabrication et observations
* `ph_estime` : DECIMAL(3,1) NULLABLE — pH calculé automatiquement
* `statut_validation` : ENUM('VALIDE', 'ERREUR_SURDOSAGE', 'CONFLIT_CHIMIQUE') DEFAULT 'VALIDE'
* `created_at`, `updated_at`, `deleted_at` : TIMESTAMPS

### 2.11. Table `formule_ingredients`
* `id` : BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `formule_id` : BIGINT UNSIGNED (FK -> `formules.id` ON DELETE CASCADE)
* `ingredient_id` : BIGINT UNSIGNED (FK -> `ingredients.id` ON DELETE RESTRICT)
* `nom_phase` : VARCHAR(50) — Phase (AQUEUSE, HUILEUSE, REFROIDISSEMENT, PHASE_A, B...)
* `pourcentage` : DECIMAL(5,2) — Proportion en %
* `cout_par_kg` : DECIMAL(10,2) DEFAULT 0.00 — Prix unitaire saisi
* `masse_grammes` : DECIMAL(8,2) — Poids calculé en g
* `created_at`, `updated_at` : TIMESTAMPS

### 2.12. Table `regles_compatibilite`
* `id` : BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `nom_regle` : VARCHAR(255)
* `groupe_a` : JSON — Liste d'ingrédients ou familles (ex: `["BTMS-50", "Polyquaternium-7"]`)
* `groupe_b` : JSON — Ingrédients antagonistes (ex: `["Texapon N70", "SCI"]`) ou condition (`__NO_ACID__`, `__HIGH_PH__`)
* `niveau` : ENUM('warn', 'error') — Avertissement ou blocage
* `message_alerte` : TEXT — Explication médicale / chimique de l'incompatibilité
* `created_at`, `updated_at` : TIMESTAMPS

### 2.13. Table `messages`
* `id` : BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `expediteur_id` : BIGINT UNSIGNED (FK -> `utilisateurs.id` ON DELETE CASCADE)
* `destinataire_id` : BIGINT UNSIGNED NULLABLE (FK -> `utilisateurs.id` ON DELETE CASCADE)
* `message` : TEXT
* `est_lu` : BOOLEAN DEFAULT FALSE
* `created_at`, `updated_at` : TIMESTAMPS

### 2.14. Table `historique_activites`
* `id` : BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `utilisateur_id` : BIGINT UNSIGNED (FK -> `utilisateurs.id` ON DELETE CASCADE)
* `action` : VARCHAR(100) — Ex: `FORMULA_CREATED`, `FORMULA_RESTORED`
* `objet_type` : VARCHAR(255) — Classe Eloquent
* `objet_id` : BIGINT UNSIGNED
* `donnees_avant` : JSON NULLABLE
* `donnees_apres` : JSON NULLABLE
* `created_at` : TIMESTAMP

---

## 3. Schéma des Relations Inter-Modèles

```
                                +-------------------+
                                |    Utilisateur    |
                                +---------+---------+
                                          |
          +-------------------------------+-------------------------------+
          | 1..*                          | 1..*                          | 1..*
          v                               v                               v
   +--------------+               +---------------+               +---------------+
   |  Abonnement  |               |    Formule    |               |    Message    |
   +--------------+               +-------+-------+               +---------------+
                                          | 1
                                          |
                                          | 1..*
                                          v
                               +---------------------+
                               | FormuleIngredient   |
                               +----------+----------+
                                          | *
                                          |
                                          | 1
                                          v
+-----------------------+        +-------------------+        +------------------------+
| CategorieIngredient   | <----+ |    Ingredient     | +----> | FicheTechnique (1..1)  |
+-----------------------+ 1   *  +--------+----------+        +------------------------+
                                          | 1
                                          | 1..*
                                          v
                               +---------------------+
                               | ProprieteIngredient |
                               +---------------------+
```

---

## 4. Données Hardcodées à Transférer vers MySQL

| Constante JS / HTML initiale | Structure MySQL Cible | Description du Transfert |
| :--- | :--- | :--- |
| `const LIBRARY` | Tables `categories_ingredients` & `ingredients` | Extraction des ~150 ingrédients répartis en catégories et phases. |
| `const INGREDIENT_SHEETS` | Tables `fiches_techniques` & `proprietes_ingredients` | Découpage des fiches INCI, solubilité, pH, températures et propriétés. |
| `const HAIRCARE_TYPES` & `SKINCARE_TYPES` | Tables `types_produits`, `cibles_ph` et `squelettes_composition` | Transfert des modèles de produits et de leurs compositions initiales. |
| `const COMPAT_RULES` | Table `regles_compatibilite` | Moteur de règles chimiques stocké en JSON relationnel. |
| `const PHASES` & `PHASE_LABELS` | Enumération / Table d'options `phases_formule` | Libellés, couleurs, icônes et styles visuels des phases. |
| `INITIAL_CLIENTS` | Table `utilisateurs` (Seeder) | Données d'essais pour l'administration et les démos. |
| `INITIAL_CHATS` | Table `messages` (Seeder) | Historique initial du chat de support. |

---

## 5. Ce qui Doit Rester dans le JavaScript (Côté Client)

* **Rendu dynamique du DOM** : Mise à jour instantanée des jauges, graphiques Donut et tableaux lors de la saisie.
* **Calculs de réactivité temps réel** : Calcul immédiat du poids en grammes pendant que l'utilisateur tape un % (évite d'attendre un appel API à chaque touche).
* **Gestion des événements UI** : Modales, filtres de recherche instantanés, animations de toast, toggle sidebar.

---

## 6. Améliorations d'Architecture Laravel

1. **`FormulationService` (`app/Services/FormulationService.php`)** :
   Centralise tous les calculs côté serveur (validation du 100%, déduction de l'eau, estimation du pH et détection des incompatibilités) avant la persistance en base de données.
2. **`FormulaObserver` (`app/Observers/FormulaObserver.php`)** :
   Enregistre automatiquement une ligne dans `historique_activites` à chaque création, modification ou suppression de formule pour garantir un Undo/Redo serveur et un versioning robuste.
3. **`SubscriptionPolicy` & `FormulaPolicy` (`app/Policies/`)** :
   Sécurise l'accès aux routes en vérifiant le statut de l'abonnement (`ACTIVE`) et la propriété des formules.
4. **`PaydunyaService` (`app/Services/PaydunyaService.php`)** :
   Remplace le paiement fictif par l'intégration d'une passerelle de paiement réelle avec gestion des Webhooks IPN.
5. **Laravel Reverb (WebSockets)** :
   Remplace la simulation du chat par une vraie messagerie en temps réel sur canal privé.

---

## 7. Migration et Seeders Proposés

### Commandes Artisan à Exécuter :
```bash
php artisan make:model CategorieIngredient -m
php artisan make:model FicheTechnique -m
php artisan make:model ProprieteIngredient -m
php artisan make:model CiblePh -m
php artisan make:model SqueletteComposition -m
php artisan make:model RegleCompatibilite -m
php artisan make:model HistoriqueActivite -m
```

### Seeders à Préparer :
* `DatabaseSeeder` : Exécute dans l'ordre :
  1. `UserSeeder` (Admin + Clients démo)
  2. `CategorieIngredientSeeder` (Tensioactifs, Humectants, Émulsifiants...)
  3. `IngredientSeeder` (150+ ingrédients avec leurs fiches techniques et propriétés)
  4. `TypeProduitSeeder` (Squelettes Leave-in, Shampoing, Crème avec leurs cibles pH)
  5. `RegleCompatibiliteSeeder` (Règles d'incompatibilité cationique/anionique, pH conservateurs)
