<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Form\ArticleType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="app_blog")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        # On va chercher la connexion au répertoire en bdd
        $repo = $doctrine->getRepository(Article::class);
        #On selectionne les éléments qui nous intéressent
        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            #On créer une variable pour afficher les données de la bdd en twig
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            "title" => "Bienvenu",
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, ManagerRegistry $doctrine)
    {
        # Avec le paramètre `Article $article` cette ligne n'est plus utile.
        // $article = new Article();
        # Cette configuration permet d'avoir deux routes et deux fonction (create et edit) en une
        # On ajoute la valeur 'null' par défaut à $article en paramètre pour que symfony soit capable d'afficher la route /new sans chercher de paramètre {id}

        dump($request);
        if(!$article) {
            # On ré-ajoute cette ligne ici pour que le nouvel article ne reste pas 'null' et sois correctement configuré
            $article = new Article();
        }

        # Création d'un objet formulaire complexe qui contient les propriétés de l'objet article et les structure
        $form = $this->createForm(ArticleType::class, $article);
                    // ->add('title', TextType::class)
                    // ->add('content', TextareaType::class)
                    // ->add('image', TextType::class)
                    // ->getForm(); 

        # Est-ce que la requête à récupérer des valeurs du formulaire ? :
        $form->handleRequest($request);

        # Vérifie validité du formulaire pour le traiter : l'enregistrer en bdd
        if($form->isSubmitted() && $form->isValid()) {
            if(!$article->getId()){
                # On passe cette ligne ici afin de gérer les deux cas de figure : création et édition sans modifiant inutilement les dates de création
                $article->setCreatedAt(new \DateTime());
            }
            // $article->setCreatedAt(new \DateTime());

            $manager = $doctrine->getManager();
            $manager->persist($article);
            $manager->flush();

            return $this->render('blog/show.html.twig', [
                'id' => $article->getId(),
                'article' => $article,
            ]);
        }

        return $this->render('blog/create.html.twig', [
            # Pour twig on utilise une fonction qui rend plus lisible et maniable l'objet $form
            'formArticle' => $form->createView(),
            # Pour modifier button d'envoi et titre selon qu'on est en création ou édition
            'editMode' => $article->getId() !== null,
        ]);
    }

     /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(int $id, ManagerRegistry $doctrine){

        $repo = $doctrine->getRepository(Article::class);
        $article = $repo->find($id);

        return $this->render('blog/show.html.twig', [
            'article' => $article,
        ]);
    }
}
