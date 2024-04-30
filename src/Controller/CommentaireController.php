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


class CommentaireController extends AbstractController
{
//************************************************* Admin ****************************************************//
    #[Route('/commentaire', name: 'app_commentaire_index', methods: ['GET'])]
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
    
    

    #[Route('/commentaire/{idc}', name: 'app_commentaire_show', methods: ['GET'])]
    public function show(Commentaire $commentaire): Response
    {
        return $this->render('commentaire/show.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/commentaire/{idc}/edit/{ide}', name: 'app_commentaire_edit', methods: ['GET', 'POST'])]
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

    #[Route('/commentaire/{idc}/{ide}', name: 'app_commentaire_delete', methods: ['GET', 'POST'])]
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


//********************************************Super Admin*******************************************************//

 #[Route('/commentairesuper', name: 'app_commentaire_index_superadmin', methods: ['GET'])]
 public function index_SA(CommentaireRepository $commentaireRepository): Response
 {
    return $this->render('commentaire_super_admin/index.html.twig', [
        'commentaires' => $commentaireRepository->findAll(),
    ]);
 }

 #[Route('/commentairesuper/new/{ide}', name: 'app_commentaire_new_superadmin', methods: ['GET', 'POST'])]
 public function newSA(Request $request, EntityManagerInterface $entityManager, $ide): Response
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

        return $this->redirectToRoute('app_evenement_show_admin', ['ide' => $ide], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('commentaire_super_admin/new.html.twig', [
        'commentaire' => $commentaire,
        'form' => $form,
    ]);}



 #[Route('/commentairesuper/{idc}', name: 'app_commentaire_show_superadmin', methods: ['GET'])]
 public function showSA(Commentaire $commentaire): Response
 {
    return $this->render('commentaire_super_admin/show.html.twig', [
        'commentaire' => $commentaire,
    ]);
 }

 #[Route('/commentairesuper/{idc}/edit/{ide}', name: 'app_commentaire_edit_superadmin', methods: ['GET', 'POST'])]
 public function editSA(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager, $ide): Response
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

        return $this->redirectToRoute('app_evenement_show_admin', ['ide' => $ide], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('commentaire_super_admin/edit.html.twig', [
        'commentaire' => $commentaire,
        'form' => $form,
    ]);
 }

 #[Route('/commentairesuper/{idc}/{ide}', name: 'app_commentaire_delete_superadmin', methods: ['GET', 'POST'])]
 public function deleteSA(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager , $ide): Response
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

    return $this->redirectToRoute('app_evenement_show_admin', ['ide' => $ide], Response::HTTP_SEE_OTHER);
 }


//************************************************* user ****************************************************//
  #[Route('/commentaireuser', name: 'app_commentaire_index_user', methods: ['GET'])]
  public function indexuser(CommentaireRepository $commentaireRepository): Response
 {
    return $this->render('commentaire_user/index.html.twig', [
        'commentaires' => $commentaireRepository->findAll(),
    ]);
 }

 #[Route('/commentaireuser/new/{ide}', name: 'app_commentaire_new_user', methods: ['GET', 'POST'])]
 public function newuser(Request $request, EntityManagerInterface $entityManager, $ide): Response
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

        return $this->redirectToRoute('app_user_show', ['ide' => $ide], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('commentaire_user/new.html.twig', [
        'commentaire' => $commentaire,
        'form' => $form,
    ]);}



#[Route('/commentaireuser/{idc}', name: 'app_commentaire_show_user', methods: ['GET'])]
public function showuser(Commentaire $commentaire): Response
{
    return $this->render('commentaire_user/show.html.twig', [
        'commentaire' => $commentaire,
    ]);
}

#[Route('/commentaireuser/{idc}/edit/{ide}', name: 'app_commentaire_edit_user', methods: ['GET', 'POST'])]
public function edituser(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager, $ide): Response
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

        return $this->redirectToRoute('app_user_show', ['ide' => $ide], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('commentaire_user/edit.html.twig', [
        'commentaire' => $commentaire,
        'form' => $form,
    ]);
 }

 #[Route('/commentaireuser/{idc}/{ide}', name: 'app_commentaire_delete_user', methods: ['GET', 'POST'])]
 public function deleteuser(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager , $ide): Response
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

    return $this->redirectToRoute('app_user_show', ['ide' => $ide], Response::HTTP_SEE_OTHER);
 }
}
