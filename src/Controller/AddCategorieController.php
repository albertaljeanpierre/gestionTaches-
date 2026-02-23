<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AddCategorieController extends AbstractController
{
    #[Route('/add/categorie', name: 'app_add_categorie')]
    public function addcategorie(Request $request, EntityManagerInterface $entityManager): Response
    {

        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            // dd($data->getnom()); 
            $entityManager->persist($categorie);
            $entityManager->flush();
            $this->addFlash('success', "La catégorie <b>{$categorie->getNom()}</b> à été ajoutée!");

            return $this->redirectToRoute('app_add_categorie');
        }
        $repoCategorie = $entityManager->getRepository(Categorie::class); 
        $categories = $repoCategorie->findAll(); 
 
        return $this->render('add_categorie/index.html.twig', [
            'form' => $form,
            'listeCategorie' => $categories,
            'nbCategories' => count($categories)
        ]);
    }
}
