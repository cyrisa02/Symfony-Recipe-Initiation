<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    /**
     * This controller displays all recipes.
     */

    /**
     * @Route("/recette", name="recipe.index", methods={"GET"})
     */
    public function index(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
         Request $request
         ): Response {
        $recipes = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    /**
     * This controller creates a new recipe.
     */

    /**
     * @Route("/recette/creation", name="recipe.new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $manager
      ): Response {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reciper = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été créée avec succès!');

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * This controller shows a form with update an recipe.
     */

    /**
     * @Route("/recette/edition/{id}", name="recipe.edit", methods={"GET","POST"})
     */
    public function edit(RecipeRepository $repository, Recipe $recipe,
     int $id,
     Request $request,
      EntityManagerInterface $manager
      ): Response {
        $recipe = $repository->findOneBy(['id' => $id]);
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifiée avec succès!');

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * This controller deletes  an recipe.
     */

    /**
     * @Route("/recipe/suppression/{id}", name="recipe.delete", methods={"GET"})
     */
    public function delete(
        EntityManagerInterface $manager,
        Recipe $recipe,
    int $id
    ): Response {
        // if (!$ingredient) {
        //     $this->addFlash(
        //         'warning',
        //         'L\'ingrédient n\'a pas été trouvé!');
        // return $this->redirectToRoute('ingredient.index');
        // } Ne fonctionne pas

        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimée avec succès!');

        return $this->redirectToRoute('recipe.index');
    }
}
