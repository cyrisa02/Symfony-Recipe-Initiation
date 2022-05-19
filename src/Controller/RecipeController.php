<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
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
     * @IsGranted("ROLE_USER")
     */
    public function index(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
         Request $request
         ): Response {
        $recipes = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    /**
     * @Route("/recette/communaute", name="recipe.community", methods={"GET"})
     */
    public function indexPublic(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $cache = new FilesystemAdapter();
        $data = $cache->get('recipes', function (ItemInterface $item) use ($repository) {
            $item->expiresAfter(15);

            return $repository->findPublicRecipe(null);
        });

        $recipes = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/community.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    /**
     * This controller creates a new recipe.
     */

    /**
     * @Route("/recette/creation", name="recipe.new", methods={"GET", "POST"})
     *  @IsGranted("ROLE_USER")
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
     * @Security("is_granted('ROLE_USER') and user === recipe.getUser()")
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

    /**
     * This controller allow us to see a recipe if this one is public.
     */
    /* @Security("is_granted('ROLE_USER') and (recipe.getIsPublic() === true || user === recipe.getUser())")
    */
    /* @Route("/recette/{id}", name="recipe.show", methods= {"GET","POST"})
    */
    public function show(
        Recipe $recipe,
        Request $request,
        MarkRepository $markRepository,
        EntityManagerInterface $manager
    ): Response {
        $mark = new Mark();
        $form = $this->createForm(MarkType::class, $mark);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mark->setUser($this->getUser())
                ->setRecipe($recipe);

            $existingMark = $markRepository->findOneBy([
                'user' => $this->getUser(),
                'recipe' => $recipe,
            ]);

            if (!$existingMark) {
                $manager->persist($mark);
            } else {
                $existingMark->setMark(
                    $form->getData()->getMark()
                );
            }

            $manager->flush();

            $this->addFlash(
                'success',
                'Votre note a bien été prise en compte.'
            );

            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);
        }

        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
        ]);
    }
}
