<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Zenstruck\Foundry\Factory;
use function Zenstruck\Foundry\faker;

class AppFixtures extends Fixture
{
    # Code du chapitre 1
    // public function load(ObjectManager $manager): void
    // {
    //     // $product = new Product();
    //     // $manager->persist($product);
    //     for($i = 1; $i <= 10; $i++)
    //     {
    //         $article = new Article();
    //         $article->setTitle("Titre n°$i")
    //                 ->setContent("<p>Contenu de l'article n°$i</p>")
    //                 ->setImage("https://placehold.co/350x150")
    //                 ->setCreatedAt(new \DateTime());
            
    //         $manager->persist($article);
    //     }

    //     $manager->flush();
    // }

    # Faker 
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        // $product = new Product();
        // $manager->persist($product);

        for ($i = 1; $i <= 3; $i++){
            $category = new Category();
            $category->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph());

            $manager->persist($category);

            for($j = 1; $j <= mt_rand(4, 6); $j++)
            {
                $article = new Article();

                // $content = '<p>';
                // $content .= join($faker->paragraphs(5), '</p><p>');
                // $content .= '</p>';
                # OU $content = '<p>' . join($faker->paragraphs(5)), '</p><p>' . '</p>';

                $article->setTitle($faker->sentence())
                        ->setContent($faker->sentence(25))
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                        ->setCategory($category);
                
                $manager->persist($article);

                for($k =  1; $k <= mt_rand(2, 5); $k++){
                    $comment = new Comment();

                    $now = new DateTime();
                    $interval = $now->diff($article->getCreatedAt());
                    $days = $interval->days;
                    $minimum = '-' . $days . 'days';

                    $comment->setAuthor($faker->name())
                            ->setContent($faker->sentence(2))
                            ->setCreatedAt($faker->dateTimeBetween($minimum))
                            ->setArticle($article);
                    
                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
