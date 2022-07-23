<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    private $entityManger;

    public function __construct(
        UserPasswordHasherInterface $encoder,
        EntityManagerInterface $entityManager
    )
    {
        $this->passwordEncoder = $encoder;
        $this->entityManager = $entityManager;

    }

    public function load(ObjectManager $manager): void
    {
        $this->users();
    }

    public function users(): void
    {
        $faker = FAKER\Factory::create('fr_FR');

        $users = Array();

        $myRoles = ['ROLE_RESTAURATEUR','ROLE_CLIENT'];
        for ($i = 0; $i < 4; $i++) {
            $users[$i] = new User();
            $users[$i]->setFirstName($faker->firstName);
            $users[$i]->setLastName($faker->lastName);
            $users[$i]->setEmail($faker->email);
            $users[$i]->setRoles([$myRoles[rand(0,1)]]);
            $users[$i]->setUserNames($faker->sentence($nbWords = 4, $variableNbWords = true));
            $users[$i]->setPassword($this->passwordEncoder->hashPassword($users[$i], '123456'));

            $this->entityManager->persist($users[$i]);
        }

        $this->entityManager->flush();
    }

}
