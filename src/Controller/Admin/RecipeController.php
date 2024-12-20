<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/les-recettes', name: 'admin.recipe.')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class RecipeController extends AbstractController
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $em
    ) {
    }

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $recipes = $this->recipeRepository->paginate($page);
        $maxPage = ceil($recipes->count() / 2);

        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
            'page' => $page,
            'maxPage' => $maxPage,
        ]);
    }

    #[Route('/{slug}-{id}', name: 'show', requirements: ['slug' => '[a-z0-9-_]+', 'id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, string $slug, int $id): Response
    {
        $recipe = $this->recipeRepository->find($id);

        if ($recipe->getSlug() !== $slug ) {
            return $this->redirectToRoute('admin.recipe.show', ['slug' => $recipe->getSlug(), 'id' => $id]);
        }

        return $this->render('admin/recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route('/ajouter', name: 'create', methods: ['GET', 'POST']), ]
    public function create(Request $request): Response
    {
        $form = $this->createForm(RecipeType::class, null, [
            'validation_groups' => [Recipe::VALIDATION_GROUP_NEW]
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Recipe $recipe */
            $recipe = $form->getData();
            $this->em->persist($recipe);
            $this->em->flush();

            $this->addFlash('success', 'La recette à bien été enregistée');

            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'edit', requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe, [
            'validation_groups' => [Recipe::VALIDATION_GROUP_EDIT]
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'la recette à bien été modifiée');

            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'remove', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function remove(Recipe $recipe): Response
    {
        $this->em->remove($recipe);
        $this->em->flush();

        $this->addFlash('success', 'La recette à bien été supprimée');

        return $this->redirectToRoute('admin.recipe.index');
    }
}
