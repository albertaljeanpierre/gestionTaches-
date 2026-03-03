<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EditController extends AbstractController
{
    #[Route('/edit', name: 'app_edit')]
    public function index(): Response
    {
        $this->addFlash('danger', '
        <p>⛔ Vous n\'avez pas acces à cette route! 
        <br> Pour éditer cliquer sur le lien d\'édition de la tache ou de la catégorie que vous souhaitez éditer.</p>');

        return   $this->redirectToRoute('app_home');

        // return $this->render('edit/index.html.twig', [
        //     'controller_name' => 'EditController',
        // ]);
    }

    #[Route('/edit/tache/{id<\d+>}', name: 'app_edit_tache')] // L'ID ne peut contenir que des chiffres 
    public function editTache(Tache $id, Request $request, EntityManagerInterface  $entityManager): Response
    {
        $repoTache =  $entityManager->getRepository(Tache::class);
        $tache =  $repoTache->find($id);
         // dd($tache ); 

        $form = $this->createForm(TacheType::class, $tache);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
 
            $tacheform =  $form->getData();
            // dd($tache ); 
            $entityManager->persist($tacheform);
            $entityManager->flush();
            $this->addFlash('success', 'Votre tache à été modifiée.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('edit/tache.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
