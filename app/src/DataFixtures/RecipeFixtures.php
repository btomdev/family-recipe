<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\Quantity;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerRestaurant\Provider\fr_FR\Restaurant;
use Symfony\Component\String\Slugger\AsciiSlugger;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Restaurant($faker));

        $date = \DateTimeImmutable::createFromMutable($faker->dateTime);
        $slugger = new AsciiSlugger();

        $ingredients = array_map(fn(string $name) => (new Ingredient())
            ->setName($name)
            ->setSlug($slugger->slug($name)), [
            "farine",
            "sucre",
            "beurre",
            "oeufs",
            "lait",
            "sel",
            "levure chimique",
            "vanille",
            "chocolat noir",
            "noix",
            "carottes",
            "oignons",
            "ail",
            "huile d'olive",
            "poulet",
            "cannelle",
            "cacao",
            "tomates",
            "basilic",
            "poivre"
        ]);

        $units = [
            "g",
            "kg",
            "l",
            "ml",
            "cl",
            "dl",
            "c. à soupe",
            "c. à café",
            "pincées",
            "verre",
        ];

        foreach ($ingredients as $ingredient) {
            $manager->persist($ingredient);
        }

        $categoryDatas = ['Plat chaud', 'Dessert', 'Entrée', 'Goûter'];
        foreach ($categoryDatas as $categoryData) {
            $category = (new Category())
                ->setName($categoryData)
                ->setSlug($slugger->slug($categoryData))
                ->setCreatedAt($date)
                ->setUpdatedAt($date);
            $manager->persist($category);

            $this->addReference($categoryData, $category);
        }

        for ($i = 1; $i <= 10; $i++) {

            $recipe = (new Recipe())
                ->setTitle($title = $faker->foodName())
                ->setSlug($slugger->slug($title)->lower())
                ->setCreatedAt($date)
                ->setUpdatedAt($date)
                ->setContent($faker->paragraphs(10, true))
                ->setCategory($this->getReference($faker->randomElement($categoryDatas), Category::class))
                ->setUser($this->getReference(UserFixtures::USER . $faker->numberBetween(1, 10), User::class))
                ->setDuration($faker->numberBetween(2, 60));

            foreach ($faker->randomElements($ingredients, $faker->numberBetween(2, 5)) as $ingredient) {
                $recipe->addQuantity((new Quantity())
                    ->setQuantity($faker->numberBetween(1, 250))
                    ->setUnit($faker->randomElement($units))
                    ->setIngredient($ingredient)
                );
            }

            $manager->persist($recipe);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
