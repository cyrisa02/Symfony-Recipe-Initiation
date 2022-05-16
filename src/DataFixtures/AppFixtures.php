<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;
    
    //Sans le Entity Listener
    //private UserPasswordHasherInterface $hasher;

    //public function __construct(UserPasswordHasherInterface $hasher)
    // {
    //     $this->faker = Factory::create('fr_FR');
    //     $this->hasher = $hasher;
    // }

    // Avec le Entity Listener

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        //Ingredients

        // A faire pour un , et voir si ça marche
        // $ingredient = new Ingredient();
        // $ingredient->setName('Ingredient #1')
        // ->setPrice(3.0);

        // $manager->persist($ingredient);

        // $manager->flush();

        //Pour créer 50 ingrédient
        // for ($i = 0; $i < 50; ++$i) {
        //     $ingredient = new Ingredient();
        //     $ingredient->setName('Ingredient'.$i)
        //     ->setPrice(mt_rand(0, 100));
        //     $manager->persist($ingredient);
        // }
        // $manager->flush();

        $ingredients = [];

        for ($i = 0; $i < 50; ++$i) {
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
            ->setPrice(mt_rand(0, 100));

            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        // Recipes
        for ($j = 0; $j < 25; ++$j) {
            $recipe = new Recipe();
            $recipe->setName($this->faker->word())
                ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
                ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setDescription($this->faker->text(300))
                ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null)
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false);

            for ($k = 0; $k < mt_rand(5, 15); ++$k) {
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }
            $manager->persist($recipe);
        }

        // Users

        for ($i = 0; $i < 10; ++$i) {
            $user = new User();
            $user->setFullName($this->faker->name())
                ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE USER'])
                ->setPlainPassword('password');
            //Supprimer car on utilise un Entity Listener
            //$hashPassword = $this->hasher->hashPassword(
            //       $user,
            //       'password'
            //  );
            // $user->setPassword($hashPassword);

            $manager->persist($user);
        }
        $manager->flush();
    }
}
