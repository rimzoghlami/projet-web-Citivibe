<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Page;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;


class EvenementController extends AbstractController
{
    #[Route('/default', name: 'app_default')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig');
    }


    #[Route('/default/courses', name: 'app_courses')]
    public function courses(): Response
    {
        return $this->render('default/courses.html.twig');
    }

    #[Route('/default/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('default/contact.html.twig', [
        ]);
    }

    #[Route('/default/trainers', name: 'app_trainers')]
    public function trainers(): Response
    {
        return $this->render('default/trainers.html.twig', [
        ]);
    }

    #[Route('/default/pricing', name: 'app_pricing')]
    public function pricing(): Response
    {
        return $this->render('default/transport.html.twig', [
        ]);
    }

    #[Route('/default/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('default/a-propos.html.twig', [
        ]);
    }

    #[Route('/default/details', name: 'app_course-details')]
    public function details(): Response
    {
        return $this->render('default/details.html.twig', [
        ]);
    }

//******************************* Partie Admin ************************************************//

  #[Route('/default/events', name: 'app_events')]
 public function events(Request $request, EvenementRepository $evenementRepository): Response
 {
    $searchTerm = $request->query->get('q');
    $category = $request->query->get('categoriee');
    $order = $request->query->get('order');


    // Récupérer les événements non expirés
    $evenementsNonExpire = $evenementRepository->findNonExpiredEvents();

    // Filtrer les événements non expirés par catégorie si spécifiée
    if ($category) {
        // Vérifier si la catégorie existe
        if (!$evenementRepository->categoryExists($category)) {
            $this->addFlash('warning', 'Cette catégorie n\'existe pas.');
            return $this->redirectToRoute('app_events');
        }

        $evenementsNonExpire = $evenementRepository->findByCategoriepAndNomContains($category, $searchTerm);
    }
    if ($order) {
        $evenementsNonExpire = $evenementRepository->trierParNom($order);
    }

    // Si aucun terme de recherche n'est spécifié, afficher tous les événements non expirés
    if (!$searchTerm) {
        return $this->render('default/evenement.html.twig', [
            'evenements' => $evenementsNonExpire,
        ]);
    }

    // Rechercher les événements non expirés par nom si un terme de recherche est spécifié
    $evenementsNonExpire = $evenementRepository->searchByNom($searchTerm);
    
    
    return $this->render('default/evenement.html.twig', [
        'evenements' => $evenementsNonExpire,
    ]);
  }



     #[Route('/evenements/historique', name: 'app_evenement_history')]
     public function eventHistory(Request $request, EvenementRepository $evenementRepository): Response
     {
      // Récupérez les événements expirés
     $evenementsExpire = $evenementRepository->findExpiredEvents();

     return $this->render('evenement_admin/evenement_history.html.twig', [
        'evenementsExpire' => $evenementsExpire,
      ]);
      }


     // #[Route('/evenement', name: 'app_evenement_idex', methods: ['GET'])]
     // public function indexE(EvenementRepository $evenementRepository): Response
     //{
     //   return $this->render('evenement/index.html.twig', [
     //        'evenements' => $evenementRepository->findAll(),
     //    ]);
     //}
   
     #[Route('/evenement/new/{idp}', name: 'app_evenement_new', methods: ['GET', 'POST'])]
     public function new(Request $request, EntityManagerInterface $entityManager, $idp): Response
     {
        // Création d'une nouvelle instance d'Evenement
        $evenement = new Evenement();
        // Création du formulaire
        $form = $this->createForm(EvenementType::class, $evenement);
    
        // Gestion de la soumission du formulaire
        $form->handleRequest($request);
    
        // Vérification de la soumission et de la validité du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
                // Check if the challenge date is expired
                $now = new \DateTime();

                // Vérifier si la date du défi est passée
                if ($evenement->getDate() < $now || ($evenement->getDate() == $now && $evenement->getHeure() < $now->format('H:i'))) {
                    $form->get('date')->addError(new FormError('La date de levenement est déjà passée.'));
                    $form->get('heure')->addError(new FormError('L\'heure de levenement est déjà passée.'));
                    return $this->renderForm('evenement_admin/new.html.twig', [
                        'evenement' => $evenement,
                        'form' => $form,
                    ]);
                }
            // Traitement de l'envoi du formulaire
    
            $photo = $form->get('photo')->getData();
            if ($photo) {
                // Generate a unique filename
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$photo->guessExtension();
    
                // Move the file to the directory where images are stored
                try {
                    $photo->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload exception
                    $this->addFlash('error', 'An error occurred while uploading the image.');
                    return $this->redirectToRoute('app_evenement_new');
                }
    
                // Set the image property in the entity to the relative path of the uploaded file
                $evenement->setPhoto('assets/img/'.$newFilename);
            }
                   
             $idp = 3; 
             $page = $entityManager->getReference(Page::class, $idp); // Debugging statement to check if the page is fetched correctly
             $evenement->setPageRelation($page);
           
             // Persistance de l'événement dans la base de données
            $entityManager->persist($evenement);
            $entityManager->flush();
            $this->addFlash('success', 'Votre evenement est ajouter avec succees
            .');
            // Redirection vers la liste des événements après la création
            return $this->redirectToRoute('app_events', [], Response::HTTP_SEE_OTHER);
        }
    
        // Affichage du formulaire de création d'événement
        return $this->renderForm('evenement_admin/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
     }
    
     #[Route('/evenement/{ide}', name: 'app_evenement_show', methods: ['GET'])]
     public function show(Evenement $evenement, EvenementRepository $evenementRepository, CommentaireRepository $commentaireRepository): Response
     {
        // Récupérer les commentaires associés à l'événement en utilisant le CommentaireRepository
        $commentaires = $commentaireRepository->findBy(['evenementRelation' => $evenement]);
        
        return $this->render('evenement_admin/show.html.twig', [
            'evenement' => $evenement,
            'commentaires' => $commentaires, // Passer les commentaires au modèle Twig
        ]);
     }


    
    
    

     #[Route('/evenement/{ide}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
     public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
     {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                // Generate a unique filename
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$photo->guessExtension();
    
                // Move the file to the directory where images are stored
                try {
                    $photo->move(
                        $this->getParameter('images_directory'),
                     $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload exception
                    $this->addFlash('error', 'An error occurred while uploading the image.');
                    return $this->redirectToRoute('app_evenement_edit');
                }
    
                // Set the image property in the entity to the relative path of the uploaded file
                $evenement->setPhoto('assets/img/'.$newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_events', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement_admin/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/evenement/{ide}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getIde(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_events', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/evenement/map', name: 'app_evenement_map')]
    public function mapaction ():Response {
        return $this->render('evenement_admin/MAP.html.twig', []);
    }


//*********************************** Partie Super Admin **********************************************//

    #[Route('/evenement/admin', name: 'app_evenement_admin')]
    public function indexA(EntityManagerInterface $entityManager): Response
    {
        $evenements = $entityManager
            ->getRepository(Evenement::class)
            ->findAll();

        return $this->render('evenement_Super_admin/index.html.twig', [
            'evenements' => $evenements, // Passer les données d'événement au template
            'controller_name' => 'EvenementController',
        ]);
    }

    #[Route('/Calender_admin', name: 'app_evenement_admin_calender', methods: ['GET'])]
    public function calender(EvenementRepository $evenementRepository): Response
    {
        $evenement = $evenementRepository->findAll();
    
        $events = [];
        foreach ($evenement as $evenement) {
            // Format the Defis data into an event
            $event = [
                'title' => $evenement->getNome(),
                
                'Date' => $evenement->getDate(),
                'start' => $evenement->getDate()->format('Y-m-d'), 
                
            ];
            $events[] = $event;
        }
    
        return $this->render('evenement_Super_admin/calender.html.twig', [
            'events' => json_encode($events), // Pass events as JSON to the template
        ]);
    }

    #[Route('/Stat_admin', name: 'app_evenement_admin_stat', methods: ['GET'])]
    public function statistiques(EvenementRepository $evenementRepository): Response
    {
        // Récupérer le nombre d'événements par catégorie
        $eventCounts = $evenementRepository->getEventCountsPerCategory();
        
        // Séparer les identifiants de catégorie et les nombres d'événements dans des tableaux séparés
        $categoryIds = [];
        $eventCountsArray = [];
        foreach ($eventCounts as $count) {
            $categoryIds[] = $count['categoryId']; // Supposant que 'categoryId' est le nom de la colonne
            $eventCountsArray[] = $count['eventCount']; // Supposant que 'eventCount' est le nom de la colonne
        }
        
        // Préparer les données pour le rendu
        $categoryData = [
            'categoryIds' => json_encode($categoryIds),
            'eventCounts' => json_encode($eventCountsArray),
        ];
        
        // Rendre le modèle avec les données de statistiques par catégorie
        return $this->render('evenement_Super_admin/stats.html.twig', $categoryData);
    }
    
      
    #[Route('/evenement/admin/new/{idp}', name: 'app_evenement_new_admin', methods: ['GET', 'POST'])]
    public function newadmin(Request $request, EntityManagerInterface $entityManager,$idp): Response
    {
        // Création d'une nouvelle instance d'Evenement
        $evenement = new Evenement();
    
        $form = $this->createForm(EvenementType::class, $evenement);
    
        // Gestion de la soumission du formulaire
        $form->handleRequest($request);
    
        // Vérification de la soumission et de la validité du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement de l'envoi du formulaire
    
            $photo = $form->get('photo')->getData();
            if ($photo) {
                // Generate a unique filename
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$photo->guessExtension();
    
                // Move the file to the directory where images are stored
                try {
                    $photo->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload exception
                    $this->addFlash('error', 'An error occurred while uploading the image.');
                    return $this->redirectToRoute('app_evenement_new_admin');
                }
    
                // Set the image property in the entity to the relative path of the uploaded file
                $evenement->setPhoto('assets/img/'.$newFilename);
            }
            $idp = 3; 
            $page = $entityManager->getReference(Page::class, $idp); // Debugging statement to check if the page is fetched correctly
            $evenement->setPageRelation($page);
          
            // Persistance de l'événement dans la base de données
           $entityManager->persist($evenement);
            $entityManager->flush();
    
            // Redirection vers la liste des événements après la création
            return $this->redirectToRoute('app_evenement_admin', [], Response::HTTP_SEE_OTHER);
        }
    
        // Affichage du formulaire de création d'événement
        return $this->renderForm('evenement_Super_admin/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }
    

    #[Route('/evenement/admin/{ide}', name: 'app_evenement_show_admin', methods: ['GET'])]
    public function showadmi(Evenement $evenement, EvenementRepository $evenementRepository, CommentaireRepository $commentaireRepository): Response
    {
        // Récupérer les commentaires associés à l'événement en utilisant le CommentaireRepository
        $commentaires = $commentaireRepository->findBy(['evenementRelation' => $evenement]);
       
    
        return $this->render('evenement_Super_admin/show.html.twig', [
            'evenement' => $evenement,
            'commentaires' => $commentaires,
        ]);
    }
    

    #[Route('/evenement/admin/{ide}/edit', name: 'app_evenement_edit_admin', methods: ['GET', 'POST'])]
    public function editadmin(Request $request, Evenement $evenement, EntityManagerInterface $entityManager ): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                // Generate a unique filename
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$photo->guessExtension();
    
                // Move the file to the directory where images are stored
                try {
                    $photo->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload exception
                    $this->addFlash('error', 'An error occurred while uploading the image.');
                    return $this->redirectToRoute('app_evenement_edit_admin');
                }
    
                // Set the image property in the entity to the relative path of the uploaded file
                $evenement->setPhoto('assets/img/'.$newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement_Super_admin/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }
    
    #[Route('/evenement/admin/{ide}', name: 'app_evenement_delete_admin', methods: ['POST'])]
    public function deleteadmin(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getIde(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_evenement_admin', [], Response::HTTP_SEE_OTHER);
    }

//******************************* Partie User ************************************************//

  #[Route('/user/events', name: 'app_user_events')]
  public function eventsuser (EvenementRepository $evenementRepository): Response
  {
    return $this->render('evenement_user/evenement.html.twig', [
        'evenements' => $evenementRepository->findAll(),
    ]);
 }
    
 #[Route('/user/events/{ide}', name: 'app_user_show', methods: ['GET'])]
 public function showuser(Evenement $evenement, EvenementRepository $evenementRepository, CommentaireRepository $commentaireRepository): Response
 {
    // Récupérer les commentaires associés à l'événement en utilisant le CommentaireRepository
    $commentaires = $commentaireRepository->findBy(['evenementRelation' => $evenement]);
    
    return $this->render('evenement_user/show.html.twig', [
        'evenement' => $evenement,
        'commentaires' => $commentaires, // Passer les commentaires au modèle Twig
    ]);
 } 
 }