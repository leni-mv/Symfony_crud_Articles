<?php

namespace App\DataFixtures;

use App\Entity\Article;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for($i = 1; $i <= 10; $i++)
        {
            $article = new Article();
            $article->setTitle("Titre n°$i")
                    ->setContent("<p>Contenu de l'article n°$i</p>")
                    ->setImage("https://placehold.co/350x150")
                    ->setCreatedAt(new \DateTime());
            
            $manager->persist($article);
        }

        $manager->flush();
    }
}
