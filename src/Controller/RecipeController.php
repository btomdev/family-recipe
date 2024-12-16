<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
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

    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['slug' => '[a-z0-9-_]+', 'id' => '\d+'], methods: ['GET'])]
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

    #[Route('/nouvelle-recettes', name: 'recipe.create', methods: ['GET', 'POST']), ]
    public function create(Request $request): Response
    {
        $form = $this->createForm(RecipeType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Recipe $recipe */
            $recipe = $form->getData();
            $this->em->persist($recipe);
            $this->em->flush();

            $this->addFlash('success', 'La recette à bien été enregistée');

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/recettes/{slug}-{id}/edit', name: 'recipe.edit', requirements: ['slug' => '[a-z0-9-_]+', 'id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'la recette à bien été modifiée');

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    #[Route('/recettes/{slug}-{id}', name: 'recipe.remove', requirements: ['slug' => '[a-z0-9-_]+', 'id' => '\d+'], methods: ['DELETE'])]
    public function remove(Recipe $recipe): Response
    {
        $this->em->remove($recipe);
        $this->em->flush();

        $this->addFlash('success', 'La recette à bien été supprimée');

        return $this->redirectToRoute('recipe.index');
    }
}
