# Support des photos iPhone (HEIC/HEIF)

## Contexte

Les iPhones modernes (depuis iOS 11) enregistrent les photos en format **HEIC/HEIF** par défaut (mode "High Efficiency"). Ce format est plus compact que JPEG mais n'est pas supporté nativement par les navigateurs web ni par la librairie PHP GD utilisée dans le pipeline d'upload.

**Symptôme avant correction :** un utilisateur iPhone envoyant sa photo directement depuis sa galerie obtenait une erreur de validation ou une image corrompue.

---

## Approche retenue : double couche

```
Utilisateur sélectionne une photo iPhone (.heic)
        │
        ▼
[Navigateur] heic2any convertit en JPEG → l'input contient un .jpg
        │
        ▼
Upload vers Symfony → validation passe (MIME = image/jpeg)
        │
        ▼
preUpload() → getimagesize() → pipeline GD existant inchangé

        ─ ─ ─ ─ ─ ─ ─ ─ ─ cas fallback ─ ─ ─ ─ ─ ─ ─ ─ ─

Si un .heic arrive quand même au serveur (navigateur incompatible)
        │
        ▼
preUpload() détecte HEIC → Image::convertHeicToJpeg() via heif-convert
        │
        ▼
Fichier converti en .jpg → pipeline GD existant reprend normalement
```

---

## Détail de chaque changement

### 1. `docker/php7-apache/Dockerfile`

**Pourquoi :** le fallback serveur a besoin de la commande `heif-convert` pour convertir les fichiers HEIC que le navigateur n'aurait pas convertis. Cette commande est fournie par le paquet Debian `libheif-examples`.

**Ce qui a changé :** ajout de `libheif-examples` dans la liste des paquets installés via `apt-get`.

```dockerfile
# Avant
RUN apt-get update && apt-get install -y \
    ...
    libkrb5-dev

# Après
RUN apt-get update && apt-get install -y \
    ...
    libkrb5-dev \
    libheif-examples      ← nouveau
```

---

### 2. `application/package.json`

**Pourquoi :** `heic2any` est la librairie JavaScript qui effectue la conversion HEIC → JPEG directement dans le navigateur, sans envoyer le fichier au serveur. Elle fonctionne via WebAssembly et supporte tous les navigateurs modernes.

**Ce qui a changé :** ajout de `"heic2any": "^0.0.4"` dans les `dependencies`.

---

### 3. `application/assets/js/front/inscription.js`

**Pourquoi :** c'est ici que se trouve le handler `change` sur l'input fichier de l'upload photo. Il faut intercepter la sélection d'un fichier HEIC et le convertir avant que le formulaire ne soit soumis.

**Ce qui a changé :**

- Import de `heic2any` en haut du fichier.
- Ajout de la fonction `isHeicFile(file)` qui détecte un fichier HEIC soit par son MIME type (`image/heic`, `image/heif`, `image/x-heic`), soit par son extension (`.heic`, `.heif`).
- Le handler `change` existant est enrichi :
  1. Si le fichier n'est pas HEIC → comportement identique à avant.
  2. Si le fichier est HEIC :
     - L'input est temporairement désactivé.
     - Un message "Conversion en cours..." s'affiche.
     - `heic2any` convertit le fichier en JPEG (qualité 92 %).
     - Un nouvel objet `File` est créé avec l'extension `.jpg`.
     - Il est réinjecté dans l'input via `DataTransfer` — le reste du formulaire et du pipeline de recadrage n'y voit que du feu.
     - L'input est réactivé.

---

### 4. `application/src/Front/Form/InscriptionPhotoBankType.php`

**Pourquoi :** la validation Symfony côté serveur refusait explicitement tout MIME type autre que PNG/JPG/GIF. Pour le fallback (navigateur incompatible), il faut que le serveur accepte les MIME types HEIC.

**Ce qui a changé :** ajout de `image/heic`, `image/heif` et `image/x-heic` dans la liste des `mimeTypes` autorisés, et mise à jour du message d'erreur.

```php
// Avant
"mimeTypes" => ["image/png", "image/jpg", "image/jpeg", "image/gif"],
"mimeTypesMessage" => "Veuillez envoyer une image au format png, jpg, jpeg ou gif..."

// Après
"mimeTypes" => ["image/png", "image/jpg", "image/jpeg", "image/gif",
                "image/heic", "image/heif", "image/x-heic"],
"mimeTypesMessage" => "Veuillez envoyer une image au format png, jpg, jpeg, gif ou heic (iPhone)..."
```

---

### 5. `application/src/Core/Entity/BanquePhoto.php`

**Pourquoi :** deux endroits à mettre à jour dans cette entité.

**Changement 1 — annotation `@Assert\File` (ligne ~92) :**
La contrainte Doctrine/Symfony qui valide le fichier uploadé doit aussi accepter les MIME types HEIC pour le fallback serveur.

```php
// Avant
mimeTypes = {"application/pdf", "application/x-pdf", "image/jpeg", "image/png"}

// Après
mimeTypes = {"application/pdf", "application/x-pdf", "image/jpeg", "image/png",
             "image/heic", "image/heif", "image/x-heic"}
```

**Changement 2 — méthode `preUpload()` :**
Après que le fichier est déplacé dans son répertoire temporaire (ou définitif), et avant l'appel à `getimagesize()`, on teste si c'est un HEIC et on le convertit en JPEG. Le nom du fichier et le type MIME stockés en base sont mis à jour en conséquence.

```php
// Nouveau bloc inséré après le move(), avant getimagesize()
if (Image::isHeic($cheminFichier)) {
    $converted = Image::convertHeicToJpeg($cheminFichier);
    if ($converted) {
        $this->setPhoto($this->getNom() . '.jpg');
        $this->setType('image/jpeg');
    }
}
```

---

### 6. `application/src/Core/Utility/Image.php`

**Pourquoi :** c'est la classe utilitaire centrale pour toutes les opérations image. On y ajoute deux méthodes statiques dédiées à la gestion HEIC.

**`isHeic(string $filepath) : bool`**
Détecte si un fichier est au format HEIC/HEIF. Vérifie d'abord l'extension, puis le MIME type réel via `mime_content_type()` (plus fiable que l'extension seule).

**`convertHeicToJpeg(string $filepath) : string|false`**
Appelle `heif-convert` (installé dans le Docker) via `exec()` pour convertir le fichier en JPEG. Si la conversion réussit, le fichier HEIC original est supprimé et le chemin du JPEG est retourné. Retourne `false` en cas d'échec (le fichier HEIC reste intact).

```php
exec('heif-convert ' . escapeshellarg($input) . ' ' . escapeshellarg($output));
```

L'argument est correctement échappé via `escapeshellarg()` pour éviter toute injection de commande.

---

## Comment tester

### Prérequis

- Docker installé et `docker-compose` disponible.
- Un fichier `.heic` de test (photo prise depuis un iPhone, ou téléchargeable depuis des sites de ressources libres de droits).

---

### Étape 1 — Rebuild du container Docker

```bash
docker-compose build
```

Vérifier dans les logs que `libheif-examples` s'installe sans erreur. On peut ensuite vérifier que la commande est bien disponible :

```bash
docker-compose exec php heif-convert --help
```

---

### Étape 2 — Installation des dépendances JS

```bash
# Dans le dossier application/
yarn install
yarn encore dev
```

Vérifier que `heic2any` apparaît dans le dossier `node_modules/` et que le build se termine sans erreur.

---

### Étape 3 — Test navigateur (chemin principal)

1. Ouvrir le formulaire d'inscription dans un navigateur moderne (Chrome, Firefox, Safari).
2. Cliquer sur le champ d'upload de photo.
3. Sélectionner un fichier `.heic`.
4. **Résultat attendu :**
   - Le message "Conversion en cours..." apparaît brièvement à côté de l'input.
   - L'input se réactive automatiquement.
   - La suite du formulaire fonctionne normalement (prévisualisation, recadrage Jcrop, rotation).
5. Soumettre le formulaire et vérifier en base de données que le champ `type` vaut `image/jpeg` et que le fichier stocké a bien l'extension `.jpg`.

---

### Étape 4 — Test du fallback serveur

Ce test simule un navigateur qui n'aurait pas converti le fichier (par exemple, un vieux Safari ou un upload via curl).

```bash
# Depuis le terminal, simuler un upload direct d'un fichier HEIC
curl -X POST https://[votre-domaine]/[route-upload] \
  -F "mjmt_front_inscription_project[banquePhoto][file]=@/chemin/vers/photo.heic;type=image/heic" \
  -F "mjmt_front_inscription_project[banquePhoto][nom]=test-heic" \
  -b "PHPSESSID=[votre-session-id]"
```

**Résultat attendu :** pas d'erreur 500, le fichier est converti en JPEG côté serveur et les dimensions sont correctement enregistrées en base.

---

### Étape 5 — Non-régression

Vérifier que les uploads JPEG et PNG existants continuent de fonctionner exactement comme avant :

1. Uploader une photo `.jpg` classique → doit fonctionner sans aucun changement de comportement.
2. Uploader une photo `.png` → idem.
3. Tenter d'uploader un fichier `.pdf` ou un format non autorisé → le message d'erreur de validation doit s'afficher.

---

### Résumé des cas couverts

| Scénario | Chemin | Résultat |
|---|---|---|
| Photo iPhone `.heic`, navigateur moderne | Conversion JS (heic2any) | ✅ JPEG envoyé au serveur |
| Photo iPhone `.heic`, vieux navigateur | Fallback serveur (heif-convert) | ✅ JPEG stocké sur disque |
| Photo `.jpg` / `.png` classique | Pipeline inchangé | ✅ Comportement identique |
| Fichier non autorisé | Validation Symfony | ✅ Erreur affichée |
