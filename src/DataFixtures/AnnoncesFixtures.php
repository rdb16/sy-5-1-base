<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Images;
use App\Entity\Annonces;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AnnoncesFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager) 
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($nbAnnonces = 1; $nbAnnonces <= 30; $nbAnnonces++){
            $user = $this->getReference('user_' . $faker->numberBetween(1,3));
            $categorie = $this->getReference('categorie_' . $faker->numberBetween(1,4));
//dump($user);
            $annonce = new Annonces();
            $date = new \Datetime();
            $annonce->setCreatedAt($date);
            $annonce->setTitle($faker->realText(25));
            $annonce->setSlug('monSlug');
            $annonce->setUsers($user);
            $annonce->setCategories($categorie);
            $annonce->setActive($faker->numberBetween(0,1));
            $annonce->setContent($faker->realText(250));

            // on upload et génère des images
            for($image = 1; $image < 3; $image++){
                //renvoie le nom de l'image qui a été crée
                $img = $faker->image('public/uploads/images/annonces');
                $imageAnnonce = new Images();
                $nomImg = basename($imageAnnonce);
                
                $imageAnnonce->setName($nomImg);
                $annonce->addImage($imageAnnonce);
            }
            //dump($annonce);
            $manager->persist($annonce);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [CategoriesFixtures::class, UsersFixtures::class];
    }
}
