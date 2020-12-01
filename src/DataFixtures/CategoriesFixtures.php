<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategoriesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categories = [
            1 => [
                'name' => 'Véhicules',
                'color' => '#ff80c0',
                'slug' => 'unSlug'
            ],
            2 => [
                'name' => 'Mobilier',
                'color' => '#80c0ff',
                'slug' => 'unSlug'
            ],
            3 => [
                'name' => 'Outils',
                'color' => '#c0ff80',
                'slug' => 'unSlug'
            ],
            4 => [
                'name' => 'Immobilier',
                'color' => '#00ff78',
                'slug' => 'unSlug'
            ],
            1 => [
                'name' => 'Sports',
                'color' => '#fd80c0',
                'slug' => 'unSlug'
            ],
        ];

        foreach($categories as $key => $value){
            
            $categorie = new Categories;
            $categorie->setName($value['name']);
            $categorie->setSlug($value['name']);
            $categorie->setColor($value['color']);
            $manager->persist($categorie);  
            //on enregistre la categorie une reference  
            $this->addreference('categorie_' . $key, $categorie);        
        }
        $manager->flush();
    }
}
