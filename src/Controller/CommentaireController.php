<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Utilisateur;
use App\Entity\Evenement;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commentaire')]
class CommentaireController extends AbstractController
{
    #[Route('/', name: 'app_commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaireRepository->findAll(),
        ]);
    }

    #[Route('/new/{ide}', name: 'app_commentaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, $ide): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'événement depuis la base de données en utilisant l'ID fourni dans l'URL
            $evenement = $entityManager->getRepository(Evenement::class)->find($ide);
            if (!$evenement) {
                throw $this->createNotFoundException('L\'événement n\'existe pas.');
            }
    
            // Récupérer l'utilisateur (supposons que l'ID soit 43)
            $userId = 43;
            $user = $entityManager->getReference(Utilisateur::class, $userId);
    
            // Associer l'événement et l'utilisateur au commentaire
            $commentaire->setEvenementRelation($evenement);
            $commentaire->setUsertRelation($user);
    
            // Enregistrer le commentaire dans la base de données
            $entityManager->persist($commentaire);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_evenement_show', ['ide' => $ide], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('commentaire/new.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);}
    
    

    #[Route('/{idc}', name: 'app_commentaire_show', methods: ['GET'])]
    public function show(Commentaire $commentaire): Response
    {
        return $this->render('commentaire/show.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/{idc}/edit/{ide}', name: 'app_commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager, $ide): Response
    {
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement = $entityManager->getRepository(Evenement::class)->find($ide);
            if (!$evenement) {
                throw $this->createNotFoundException('L\'événement n\'existe pas.');
            }
            $commentaire->setEvenementRelation($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_show', ['ide' => $ide], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);
    }

    #[Route('/{idc}/{ide}', name: 'app_commentaire_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager , $ide): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getIdc(), $request->request->get('_token'))) {
            $evenement = $entityManager->getRepository(Evenement::class)->find($ide);
            if (!$evenement) {
                throw $this->createNotFoundException('L\'événement n\'existe pas.');
            }
            $commentaire->setEvenementRelation($evenement);
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_show', ['ide' => $ide], Response::HTTP_SEE_OTHER);
    }
}
