<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    /**
     * This controller display all ingredients.
     */

    /**
     * @Route("/ingredient", name="app_ingredient", methods={"GET"})
     */
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        // dd($ingredients); contrôle sur la page que les données apparaissent

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }

    /**
     * This controller show a form with create an ingredient.
     */

    /**
     * @Route("/ingredient/nouveau", "ingredient.new", methods={"GET","POST"})
     */
    public function new(Request $request,
    EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        /*
         * Test 1
         *
         * Mettre Jambon et 37,20 dans le formulaire
         * $form->handleRequest($request);
         *   if ($form->isSubmitted() && $form->isValid()) {
         *      dd($form->getData());
         * }
        */

        /*
         * Test 2
         *
         * $form->handleRequest($request);
        *if ($form->isSubmitted() && $form->isValid()) {
         *   $ingredient = $form->getData();
          *  dd($ingredient);
        *}
         *
         */

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été créé avec succès!');

            //return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * This controller shows a form with update an ingredient.
     */

    /**
     * @Route("/ingredient/edition/{id}", "ingredient.edit", methods={"GET","POST"})
     */
    public function edit(IngredientRepository $repository, Ingredient $ingredient,
     int $id,
     Request $request,
      EntityManagerInterface $manager
      ): Response {
        $ingredient = $repository->findOneBy(['id' => $id]);
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès!');

            //return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ingredient/suppression/{id}", "ingredient.delete", methods={"GET"})
     */
    public function delete(
        EntityManagerInterface $manager,
        Ingredient $ingredient,
    int $id
    ): Response {
        // if (!$ingredient) {
        //     $this->addFlash(
        //         'warning',
        //         'L\'ingrédient n\'a pas été trouvé!');
        // return $this->redirectToRoute('ingredient.index');
        // } Ne fonctionne pas

        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimé avec succès!');

        //return $this->redirectToRoute('ingredient.index');

        //return $this->render('pages/ingredient/new.html.twig', [
          //  'form' => $form->createView(),
        //]);
    }
}
