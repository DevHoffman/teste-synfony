<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('pt_BR');

        for ($i = 0; $i < 10; $i++) {
            $user = new Users();
            $user->setNome($faker->name);
            $user->setLogin($faker->userName);
            $user->setSenha($faker->password);
            $user->setEmail($faker->email);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
