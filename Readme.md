# Créer un affichage d'articles dynamique avec un système d'authentification et de crud pour l'administrateur non développeur du site

Ce projet est un projet vitrine dans le but d'avoir un code propre et cohérent sur lequel on puisse s'appuyer lors du développement d'autres projets. Ce projet est fait en suivant le tuto en 4partie de Lior CHAMLA :
- Mise en place du projet : https://www.youtube.com/watch?v=UTusmVpwJXo&list=PLpUhHhXoxrjdQLodxlHFY09_9XzqdPBW8
- Formulaire : https://www.youtube.com/watch?v=_cgZheTv-FQ&list=PLpUhHhXoxrjdQLodxlHFY09_9XzqdPBW8&index=2
- Relations entre entités : https://www.youtube.com/watch?v=e5udJTjbYzw&list=PLpUhHhXoxrjdQLodxlHFY09_9XzqdPBW8&index=3
- Authentification : https://www.youtube.com/watch?v=_GjHWa9hQic&list=PLpUhHhXoxrjdQLodxlHFY09_9XzqdPBW8&index=4

## Environnement

- php 7.4.16
- Composer 2.3.10
- Symfony CLI
- Symfony 5.4
- MAMP pour la base de données : localhost/phpMyAdmin & http://localhost/MAMP/index.php
- git & github

Commandes utiles :
- `symfony server:start` : pour démarrer le serveur
- `php bin/console` : console php pour générer les make:entity par exemple
- `composer` / `composer require name_package` : pour installer des paquets

## Intégration Git - Github

Git commandes :
- Pas besoin de git init car git intégré avec le projet.
- git add -A
- git commit -am "First commit"

Github commandes :
- git branch -M 'main'
- git remote add origin https://github.com/leni-mv/Symfony_crud_Articles.git
- git push -u origin 'main'

## Suivi du projet et notes personnelles

### Mise en place du projet 
- **Template** :
    - `php bin/console make:controller` : BlogController
    - Toutes les routes sont configurée dans **BlogController** mais voici le détail :
    - Bootswatch.com > theme Flatly > `https://bootswatch.com/5/flatly/bootstrap.min.css` > on copie colle cette adresse en **base.html.twig > head > link > src**
    - On reprnd la navbar qui nous intéresse dans **base.html.twig > body** (et on peut faire la même chose pour tous les éléments disponibles qui nous intéressent)
    - Pour les images : `https://placehold.co/widthxheight`
- **Organisation du site** :
    - Logo renvoi à page home qui constitu l'accueil `/`
    - ``index.html.twig`` affiche les articles et son name de route est `/blog`
    - **index.html.twig > lire la suite** : renvoient à un article rendom créer dans `show.html.twig` avec le name route `/blog/12`
- **Database et entity Article** :
    - Création du fichier `.env.local` pour des raisons de sécurité
    - On connect notre database MAMP dans ce fichier avec le nom de notre future bdd **blog**
    - `doctrine:database:create` créer le bdd **blog**
    - `php bin/console make:entity` Pour créer l'entité **Article**
    - On lui passe les propriétés `title(string), content(text), image(string), createdAt(dateTime)`
- **Fixtures** :
    - `composer require --dev orm-fixtures`
    - **src > DataFixtures > AppFixtures.php** créer
    - Code arrangé à notre sauce
    - **Note!!** : pour le `setCreatedAt` on met un ``\ ``devant ``DateTime`` pour que php comprenne que c'est la fonction datetime et qu'il puisse l'utiliser
    - `php bin/console doctrine:fixtures:load` > yes : pour enregistrer les données flush(dans AppFixtures) en bdd
- **Repository pour afficher liste d'articles**
    - **src/Repository/ArticleRepository.php** a été créer en même temps que l'entité Article
    - Dans **BlogController > function index()** on va créer un code qui permet d'afficher nos articles en bdd (code commenté si vous voulez voir)
    - Les variables ``| date()`` et ``| raw`` :
        - ``date`` : permet de formater la donnée `createdAt` (équivalent twig de la fonction ->format() php)
        - ``raw`` : permet d'afficher le contenu en html
    - Maintenant on saouhaite les afficher dans la fonction ``show()`` de la même manière
    - Un paramètre ``id`` est ajouter à la route dans le controlleur
    - **Note!!** : ce paramètre doit être ajouté en twig en second argument de la fonction `path` pour fonctionner : `{{ path('blog_show', {id: article.id}) }}` (sans ça symfony nous retourne une erreur)

### Formulaire 

- **BlogController > form()** création de la fonction form
- **Note!!** : dans la route `blog/new` 'new' peut être interprété par symfony comme un identifiant `{id}` pour la fonction ``show`` au dessus. Pour éviter ce cas on fait remonter la fonction ``form()`` au dessus de la fonction `show()` ou on met un ordre de priorité (cf doc symfony)
- **créer le formulaire**
    - Pour le back tous se passe dans BlogController > form() et le code est commenté
    - Pour le front :
        - Twig : **create.html.twig**
        - Bootstrap : **config > package > twig.yalm >** twig : ``form_themes: ['bootstrap_5_layout.html.twig']`` ajoutée
    - On instancie un nouvel article avec ces fonctions set ``$article = new Article()``
    - On créer l'objet formulaire ``createFormBuilder($article)``
- **Traiter le formulaire**
    - On aura ensuite besoin de passer en paramètre la fonction `Request $request` pour récupérer les informations envoyée par le formulaire `$form->handleRequest($request);`
    - On vérifie que le formulaire isSubmitted et isValid
    - Si oui on appel en paramètre de fonction `ManagerRegistry $doctrine` pour appeler `$manager = $doctrine->getManager();`
    - On persist, on flush et on `return` une page pour montrer le nouveau rendu de l'article
    - **Note!!** : A partir de ``ManagerRegistery`` deux type de lignes :
        - `$repo = $doctrine->getRepository(Article::class);` pour récupérer des infos en base de donnée au format article
        - `$manager = $doctrine->getManager();` pour se lier à la bdd et **persist / flush** un nouvel objet
    - Paramètre `editMode` code intéressant : permet de modifier titre et boutton d'envoi en twig
- **Méthode en console**
    - `php bin/console make:form` on lie à l'entité ``Article``
    - Créer fichier formtype : src > Form > ArticleType.php : construit le formulaire
    - la fonction `createFormBuilder($article)` devient `createForm(ArticleType::class, $article)` car le constructeur se trouve maintenant dans le fichier formtype
- **Validation** :
    - Dans **entity > Article.php** ajout de `use Symfony\Component\Validator\Constraints as Assert;`
    - Dans doc symfony **Advanced Topic > Validation > Constraint > Lenght** : Ajout d'une longueur de 10 minimum au title de article
    - Autant de possibilités que ce qui est présenté dans la doc
    - Pour aller plus loin {{ form_error(formArticle.title)}} permet de styliser les différentes erreurs

