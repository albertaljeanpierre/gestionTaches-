<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AddTacheController extends AbstractController
{
    #[Route('/add/tache', name: 'app_add_tache')]
    public function addTache(Request $request, EntityManagerInterface $entityManager): Response
    {

        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $task = $form->getData();
            $entityManager->persist($tache);
            $entityManager->flush();
            $this->addFlash('success', 'Votre tache à été ajoutée!');

            return $this->redirectToRoute('app_home');
        }


        return $this->render('add_tache/index.html.twig', [
            'form' => $form,
        ]);
    }
}
