<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Users;
use App\Entity\UserPhoto;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {           
        $faker = Faker\Factory::create('fr_FR');

        for($nbUsers = 1; $nbUsers <= 3; $nbUsers++){
            $user = new Users();
            $user->setEmail($faker->email);
            // on veut que le 1° soit admin, pas les autres
            if($nbUsers === 1){
                $user->setRoles(['ROLE_ADMIN']);
            }else{
                $user->setRoles(['ROLE_USER']);
            }
            $user->setPassword($this->encoder->encodePassword($user,'azerty'));
            $user->setName($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setIsVerified(1);
            $userPhoto = new UserPhoto();
            $userPhoto->setName('smiley-yeux-bleus.jpg');
            $user->setUserPhoto($userPhoto);
            $manager->persist($user);

            // on ajoute eune ref
            $this->addReference('user_' . $nbUsers, $user);
            //dump($user);
        }

        $manager->flush();
    }
}
