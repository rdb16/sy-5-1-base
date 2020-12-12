<?php

namespace App\DataFixtures;

use App\Entity\Agences;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AgencesFixtures extends Fixture 
{
    public function load(ObjectManager $manager)
    {
        $agences = [
            1 => [
                'nom' => 'Paris',
                'latitude' => '48.8529',
                'longitude' => '2.35',
                'address' => '23 rue des stroumphs 75002'
            ],
            2 => [
                'nom' => 'Brest',
                'latitude' => '48.383',
                'longitude' => '-4.50',
                'address' => '23 rue du Tonnerre 29002'
            ],
            3 => [
                'nom' => 'Quimper',
                'latitude' => '48.000',
                'longitude' => '-4.10',
                'address' => '23 rue des bigoudins 29000'
            ],
            4 => [
                'nom' => 'Bayonne',
                'latitude' => '43.5009',
                'longitude' => '-1.467',
                'address' => '15 rue dujambon'
            ],  
        ];

        foreach($agences as $key => $value){
            
            $agence = new Agences;           

            $agence->setNom($value['nom']);
          
            $agence->setLatitude($value['latitude']);
            
            $agence->setLongitude($value['longitude']);

            $agence->setAddress($value['address']);
            
            $manager->persist($agence);  
        }
        $manager->flush();
    }
   
}
