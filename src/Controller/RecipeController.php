<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly EntityManagerInterface $em
    ) {
    }

    #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request): Response
    {
        $recipes = $this->em->getRepository(Recipe::class)->findWithDurationLowerThan(60);

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['slug' => '[a-z0-9-_]+', 'id' => '\d+'])]
    public function show(Request $request, string $slug, int $id): Response
    {
        $recipe = $this->em->getRepository(Recipe::class)->find($id);

        if ($recipe->getSlug() !== $slug ) {
            return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $id]);
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }
}
