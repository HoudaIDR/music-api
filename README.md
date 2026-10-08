<div align="center">

# 🎵 Music API

### Une API REST pour explorer, gérer et évaluer des artistes et leurs albums.

<br>

![PHP](https://img.shields.io/badge/PHP-8-777BB4?style=for-the-badge\&logo=php\&logoColor=white)
![Slim](https://img.shields.io/badge/Slim-4-74A045?style=for-the-badge)
![MySQL](https://img.shields.io/badge/MySQL-PDO-4479A1?style=for-the-badge\&logo=mysql\&logoColor=white)
![JWT](https://img.shields.io/badge/Auth-JWT-000000?style=for-the-badge\&logo=jsonwebtokens\&logoColor=white)
![AlwaysData](https://img.shields.io/badge/Hosted%20on-AlwaysData-E8467C?style=for-the-badge)

<br>

**PHP 8 · Slim 4 · MySQL · PDO · JWT · Repository Pattern**

<br>

[🚀 Démarrage rapide](#-démarrage-rapide) ·
[🔐 Authentification](#-authentification) ·
[📡 Endpoints](#-endpoints) ·
[🏗️ Architecture](#️-architecture)

</div>

---

## 🎧 À propos

**Music API** est une API REST développée en **PHP 8** avec le framework **Slim 4**.

Elle permet de consulter des **artistes**, leurs **albums** ainsi que les **évaluations associées**.

L'application repose sur une architecture organisée autour du **Repository Pattern** afin de séparer la logique d'accès aux données du reste de l'application.

L'API est également protégée par une authentification **JWT (JSON Web Token)** : toutes les routes `/api/*` nécessitent un token valide.

> 🌐 **API disponible en ligne :**
> `https://houda.alwaysdata.net/music-api`

---

## ✨ Fonctionnalités

| Fonctionnalité | Description                  |
| :------------: | ---------------------------- |
|       🎤       | Gestion des artistes         |
|       💿       | Consultation des albums      |
|        ⭐       | Consultation des évaluations |
|       🔐       | Authentification JWT         |
|       🗄️      | Persistance MySQL avec PDO   |
|       🧱       | Architecture Repository      |
|       🌐       | API REST accessible en ligne |
|       🚀       | Hébergement AlwaysData       |

---

## 🚀 Démarrage rapide

### 1 · Obtenir un token JWT

Avant d'utiliser les endpoints de l'API, authentifiez-vous via `/login`.

```bash
curl -X POST https://houda.alwaysdata.net/music-api/login \
  -H "Content-Type: application/json" \
  -d '{"username": "SaintMichel", "password": "ITcampus"}'
```

La réponse contient votre token :

```json
{
  "status": "success",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOi..."
}
```

### 2 · Utiliser le token

Le token doit ensuite être transmis dans le header `Authorization` :

```http
Authorization: Bearer <votre_token>
```

Par exemple :

```bash
curl https://houda.alwaysdata.net/music-api/api/artists \
  -H "Authorization: Bearer <votre_token>"
```

> 🎉 **Vous êtes prêt à utiliser l'API.**

---

# 🔐 Authentification

L'authentification repose sur des **JSON Web Tokens (JWT)**.

### `POST /login`

|                      |                    |
| -------------------- | ------------------ |
| **Méthode**          | `POST`             |
| **Endpoint**         | `/login`           |
| **Content-Type**     | `application/json` |
| **Authentification** | ❌ Non requise      |

#### 📥 Corps de la requête

```json
{
  "username": "SaintMichel",
  "password": "ITcampus"
}
```

#### 📤 Réponse — `200 OK`

```json
{
  "status": "success",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOi..."
}
```

Une fois obtenu, le token doit être envoyé avec chaque requête vers `/api/*`.

```http
Authorization: Bearer <votre_token_jwt>
```

> 💡 **Apache / AlwaysData**
> Le passage du header `Authorization` est assuré par la configuration `.htaccess`.

---

# 📡 Endpoints

### 🎤 Artistes

**Base :** `/api/artists`

|   Méthode   | Route               | Description             | Réponse |
| :---------: | ------------------- | ----------------------- | :-----: |
|   🟢 `GET`  | `/api/artists`      | Liste tous les artistes |  `200`  |
|   🟢 `GET`  | `/api/artists/{id}` | Détails d'un artiste    |  `200`  |
|  🟡 `POST`  | `/api/artists`      | Ajoute un artiste       |  `201`  |
| 🔴 `DELETE` | `/api/artists/{id}` | Supprime un artiste     |  `204`  |

<details>
<summary><b>📤 Exemple — Ajouter un artiste</b></summary>

<br>

**POST `/api/artists`**

```json
{
  "Name": "Daft Punk"
}
```

</details>

---

### 💿 Albums

**Base :** `/api/albums`

|  Méthode | Route                      | Description           | Réponse |
| :------: | -------------------------- | --------------------- | :-----: |
| 🟢 `GET` | `/api/albums`              | Liste tous les albums |  `200`  |
| 🟢 `GET` | `/api/albums/{id}`         | Détails d'un album    |  `200`  |
| 🟢 `GET` | `/api/artists/{id}/albums` | Albums d'un artiste   |  `200`  |

---

### ⭐ Évaluations

**Base :** `/api/ratings`

|  Méthode | Route                      | Description                  | Réponse |
| :------: | -------------------------- | ---------------------------- | :-----: |
| 🟢 `GET` | `/api/ratings`             | Liste toutes les évaluations |  `200`  |
| 🟢 `GET` | `/api/albums/{id}/ratings` | Évaluations d'un album       |  `200`  |

---

# 🏗️ Architecture

L'API suit une architecture simple basée sur le **Repository Pattern**.

```text
                         ┌──────────────────┐
                         │   Client HTTP    │
                         └────────┬─────────┘
                                  │
                                  ▼
                         ┌──────────────────┐
                         │   Middleware JWT │
                         └────────┬─────────┘
                                  │
                           Token valide
                                  │
                                  ▼
                         ┌──────────────────┐
                         │      Routes      │
                         └────────┬─────────┘
                                  │
                                  ▼
                         ┌──────────────────┐
                         │    Repository   │
                         └────────┬─────────┘
                                  │
                                  ▼
                         ┌──────────────────┐
                         │   MySQL / PDO   │
                         └──────────────────┘
```

### 🔧 Stack technique

| Domaine              | Technologie            |
| -------------------- | ---------------------- |
| 💻 Langage           | **PHP 8**              |
| ⚡ Framework          | **Slim 4**             |
| 🔐 Authentification  | **firebase/php-jwt**   |
| 🗄️ Base de données  | **MySQL**              |
| 🔌 Accès aux données | **PDO**                |
| 🧱 Architecture      | **Repository Pattern** |
| 📡 Standards HTTP    | **PSR-7**              |
| 💉 Conteneur         | **PSR-11**             |
| 🌐 Serveur           | **Apache**             |
| ☁️ Hébergement       | **AlwaysData**         |
| 📦 Versioning        | **Git / GitHub**       |

---

# 📁 Structure du projet

```text
music-api/
│
├── public/
│   └── index.php
│
├── src/
│   ├── Middleware/
│   │   └── JwtMiddleware.php
│   │
│   ├── Repository/
│   │   ├── ArtistRepository.php
│   │   ├── AlbumRepository.php
│   │   └── RatingRepository.php
│   │
│   └── ...
│
├── .htaccess
├── composer.json
├── composer.lock
└── README.md
```

> 📌 *La structure peut évoluer selon les besoins du projet.*

---

# 🧪 Exemple d'utilisation

Une fois authentifié, il est possible de récupérer les artistes disponibles :

```bash
curl https://houda.alwaysdata.net/music-api/api/artists \
  -H "Authorization: Bearer <votre_token>"
```

Puis de consulter les albums d'un artiste :

```bash
curl https://houda.alwaysdata.net/music-api/api/artists/1/albums \
  -H "Authorization: Bearer <votre_token>"
```

Et enfin les évaluations d'un album :

```bash
curl https://houda.alwaysdata.net/music-api/api/albums/1/ratings \
  -H "Authorization: Bearer <votre_token>"
```

---

# 🔒 Sécurité

L'API utilise plusieurs mécanismes pour sécuriser l'accès aux données :

* 🔐 **JWT** pour l'authentification
* 🛡️ **Middleware** pour protéger les routes
* 🔑 Header `Authorization: Bearer`
* 🗄️ **PDO** pour l'accès à MySQL
* ⚙️ Configuration Apache via `.htaccess`

Les routes `/api/*` ne sont accessibles qu'avec un **token JWT valide**.

---

# 🌐 Déploiement

L'API est actuellement hébergée sur **AlwaysData** avec un serveur **Apache**.

### URL

```text
https://houda.alwaysdata.net/music-api
```

### Environnement

```text
PHP 8
Slim 4
Apache
MySQL
AlwaysData
```

---

<div align="center">

## 🎵 Built with PHP & Slim

**Music API**

Développé par **Houda**

[@HoudaIDR](https://github.com/HoudaIDR)

<br>

⭐ **Merci d'avoir consulté le projet !**

</div>
