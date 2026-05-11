# ACF Setup GC Digital Arts

## 1. Structure WordPress mise en place

- CPT `project` pour les projets du portfolio.
- CPT `experience` pour la timeline expérience / formation.
- Taxonomie `gc_category` partagée entre projets, posts et expériences.
- Taxonomie `position` pour les rôles métier.
- Taxonomie `technology` pour les outils et stacks (incluant les médias de la bibliothèque).
- Templates dédiés pour la home, les expertises, l'expérience, la galerie, le contact, les archives projets et les singles.
- Switcher de langue `FR/EN` avec détection automatique par langue navigateur: FR par défaut si locale `fr-*`, sinon EN.

## 2. Pages à créer dans le back-office

- `Accueil` et la définir comme page d'accueil statique.
- `Expérience` avec le template `Experience`.
- `Galerie` avec le template `Galerie`.
- `Contact` avec le template `Contact`.
- Une page par expertise avec le template `Expertise` : `Front-end development`, `UX-UI design`, `Game design`, `Video production`, `3D production`, `2D production`, `Pixel art`.
- Une page de blog puis la définir comme page des articles.

## 3. Groupes de champs ACF recommandés

### Groupe `Home sections`
Localisation: `Page` is equal to `Accueil`

- `home_hero_title` : Text
- `home_hero_intro` : Textarea
- `presentation_title` : Text
- `presentation_text` : Wysiwyg ou Textarea
- `portfolio_title` : Text
- `blog_title` : Text
- `contact_anchor_label` : Text

### Groupe `Project details`
Localisation: `Post Type` is equal to `project`

- `start_date` : Date picker, format de retour `Ymd`
- `end_date` : Date picker, format de retour `Ymd`
- `main_visual` : File ou Image, format de retour `Array`
- `project_gallery` : Gallery, format de retour `ID` ou `Array`
- `text_fr` : Wysiwyg
- `text_en` : Wysiwyg

En plus, l'ecran d'edition projet inclut une metabox native `Dates du projet` avec deux date selectors:

- `Start date` (meta `start_date`)
- `End date` (meta `end_date`)

Les champs `Positions`, `Technologies`, `Categories` sont portés par les taxonomies `position`, `technology`, `gc_category`.

- `Technologies` est configure en taxonomie hierarchique pour afficher des cases a cocher dans les settings projet.
- Chaque projet dispose aussi d'un select unique `Experience liee` (metabox native) pour associer le projet a une experience precise.

### Groupe `Post enrichi`
Localisation: `Post Type` is equal to `post`

- `main_visual` : File ou Image, format de retour `Array`
- `text_fr` : Wysiwyg
- `text_en` : Wysiwyg
- `related_projects` : Relationship, filtré sur le post type `project`
- `Experience liee` : select unique via metabox native (meta `related_experience`)

Les taxonomies `technology` et `gc_category` doivent être associées au post.

### Groupe `Experience details`
Localisation: `Post Type` is equal to `experience`

- `company_name` : Text
- `start_date` : Date picker, format de retour `Ymd`
- `end_date` : Date picker, format de retour `Ymd`
- `position_label` : Text
- `company_logo` : Image, format de retour `Array`

Tu peux aussi remplacer `position_label` par la taxonomie `position` si tu veux garder une donnée complètement normalisée.

### Groupe `Expertise page`
Localisation: `Page Template` is equal to `template-expertise.php`

- `expertise_category` : Taxonomy field lié à `gc_category`, format de retour `Term object`

Chaque page expertise doit pointer vers une seule catégorie principale pour filtrer automatiquement les projets affichés.

### Groupe `Contact options`
Localisation: `Options Page` is equal to `Contact`

- `contact_email` : Email
- `contact_phone` : Text
- `contact_location` : Text
- `contact_intro` : Textarea
- `contact_form_shortcode` : Textarea

### Groupe `Global options`
Localisation: `Options Page` is equal to `Réglages globaux`

- `footer_text` : Textarea

### Groupe `Social links`
Localisation: `Options Page` is equal to `Réseaux sociaux`

- `social_links` : Repeater
- Sous-champ `label` : Text
- Sous-champ `url` : URL

### Groupe `Technology term`
Localisation: `Taxonomy Term` is equal to `technology`

- `technology_logo` : Image

### Paramètres médias (sans ACF)
Localisation: `Médias` > modifier un média

Le thème ajoute automatiquement un champ `Type de media` en choix unique (radio):

- `media`
- `logo`
- `background`

Les champs affichés changent selon le type sélectionné. Un seul groupe apparaît à la fois:

- Type `media`:
- `Texte FR`
- `Texte EN`
- `Annee`
- `Projet lie` (menu déroulant, un seul projet)
- `Post lie` (menu déroulant, un seul post)
- `Categories` (cases à cocher, multi-sélection)
- `Technologies` (cases à cocher, multi-sélection)

- Type `logo`:
- `Experience liee` (menu déroulant, une seule expérience)

- Type `background`:
- `Usage background` (`global`, `home`, `experience`, `blog`, `contact`)

Les taxonomies `gc_category` et `technology` sont également disponibles sur les médias (attachments), donc éditables directement depuis l'onglet Médias.

Actions de masse ajoutées dans `Médias` (vue liste):

- `Assigner un projet (GC)`
- `Définir texte FR/EN (GC)`

L'action `Assigner un projet (GC)` accepte aussi une catégorie et une technologie via les listes déroulantes ajoutées en haut de l'écran médias.

Cela couvre la demande "chaque technologie pourra être associée à un logo".

## 4. Ce que fait déjà le thème avec ces champs

- Home : sections Accueil, Présentation, Expertise, Portfolio, Blog, Contact avec navigation sticky et pastilles.
- Menu sticky : géré dans le header. Le dropdown `Expertise` se règle dans Apparence > Menus avec des sous-éléments.
- Pages expertise : affichent automatiquement les projets de la catégorie choisie via `expertise_category`.
- Page Expérience : affiche la timeline des expériences puis un bloc portfolio. Les logos de type `logo` liés à une expérience sont affichés automatiquement.
- Blog : filtre par année, catégorie et projet lié via `related_projects`.
- Galerie : filtre par année, catégorie, projet et technologie. Elle agrège les médias de la bibliothèque marqués en type `media`.
- Fiches projet / post : affichent visuel principal, textes FR/EN, taxonomies et relations croisées.
- Fiche projet : affiche la galerie intégrée (`project_gallery`) puis les médias de type `media` liés depuis la bibliothèque.
- Backgrounds : les médias de type `background` sont utilisés pour l'habillage du site selon leur `Usage background`.
- Langue: le switcher FR/EN est visible dans le header et mémorisé via cookie (`gc_lang`).

## 5. Points de vigilance back-office

- Pour que les dates se trient correctement, utilise toujours le format de retour ACF `Ymd`.
- Pour `main_visual`, retourne un `Array`, sinon le rendu image/vidéo sera moins fiable.
- Le filtre `projet` du blog dépend du champ `related_projects` sur les posts.
- Le filtre `projet` de la galerie dépend de `related_project` sur les médias de type `media`.
- Le menu dropdown `Expertise` n'est pas généré automatiquement: crée-le dans Apparence > Menus avec les pages expertise en enfants.
- La galerie d'un projet repose sur `project_gallery` (assets internes du projet) et/ou les médias de type `media` liés via `related_project`.

## 6. Ordre conseillé d'intégration

1. Créer les pages et assigner les templates.
2. Créer les groupes ACF ci-dessus.
3. Renseigner d'abord les options globales et contact.
4. Créer les taxonomies puis les projets.
5. Relier les posts aux projets avec `related_projects`.
6. Construire le menu principal avec un item parent `Expertise` et les pages expertise en sous-menu.