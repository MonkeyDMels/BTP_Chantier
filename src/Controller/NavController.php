<?php

namespace App\Controller;

use App\Controller\Admin\PrestataireCrudController;
use App\Entity\Chantier;
use App\Entity\Prestataire;
use App\Entity\Tache;
use App\Entity\User;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NavController extends AbstractController
{
    /**
    *@Route("/btp", name="btp")
    */
    public function index():Response
    {
        return $this->render('btp/index.html.twig', [
            'controller_name' => 'NavController',
        ]);
    }
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('btp/home.html.twig');
    }

    /**
    *@Route("/cdt", name="dashboard_conducteur")
    */
    public function dashboardCdt(): Response
    {
        $repoUser = $this->getDoctrine()->getRepository(User::class);
        $repoPresta = $this->getDoctrine()->getRepository(Prestataire::class);


        /** @var User|null $user */
        $user = $repoUser->findOneBy([
            'email' => $this->getUser()->getUserIdentifier()
        ]);

        $chantiers = $user->getChantiers();

        $prestataires = $repoPresta->findAll();

        return $this->render('btp/cdt/dashboard_cdt.html.twig',[
            'chantiers' => $chantiers,
            'prestataires' => $prestataires,
        ]);
    }

    /**
     * @Route("/presta", name="dashboard_prestataire")
     */
    public function dashboardPresta(): Response
    {
        $repoTache = $this->getDoctrine()->getRepository(Tache::class);

        $taches = $repoTache->findAll();

        $tachesPlan = $this->getDoctrine()
            ->getManager()
            ->createQuery('SELECT t FROM App:Tache t WHERE t.dateDebut > CURRENT_DATE() AND t.dureeReelle IS NULL' )
            ->getResult();

        $tachesCours = $this->getDoctrine()
            ->getManager()
            ->createQuery('SELECT t FROM App:Tache t WHERE t.dateDebut < CURRENT_DATE() AND t.dureeReelle IS NULL' )
            ->getResult();


        return $this->render('btp/presta/dashboard_presta.html.twig', [
            'taches' => $taches,
            'tachesPlan' => $tachesPlan,
            'tachesCours' => $tachesCours
        ]);

    }


    /**
     * @Route("/cdt/chantier/{id}", name="consult_chantier")
     */
    public function consultChantier($id): Response
    {
        $repo = $this->getDoctrine()->getRepository(Chantier::class);

        $chantier = $repo->find($id);

        return $this->render('btp/cdt/consult_chantier.html.twig',[
            'chantier' => $chantier,
            'phases' => $chantier->getPhases(),
        ]);
    }

    /**
     * @Route("/crea_chantier", name="add_chantier")
     */
    public function addChantier(): Response
    {
        return $this->render('btp/add_chantier.html.twig');

    }
//    /**
//     * @Route("/planning", name="planning_chantier")
//     */
//    public function gantt(): Response
//    {
//        $repo = $this->getDoctrine()->getRepository(Chantier::class);
//
//        $chantiers = $repo->findAll();
//
//        return $this->render('btp/planning.html.twig',[
//            'chantiers' => json_encode($chantiers),
//        ]);
//    }

    /**
     * @Route("/cdt/prestataire/{id}", name="consult_prestataire")
     */
    public function consultPrestataire($id): Response
    {
        $repo = $this->getDoctrine()->getRepository(Prestataire::class);

        $prestataire = $repo->find($id);

        return $this->render('btp/cdt/consult_prestataire.html.twig',[
            'prestataire' => $prestataire,
        ]);
    }

    /**
     * @Route("/maitreouvrage", name="dashboard_maitreouvrage")
     */
    public function dashMo(): Response
    {
        $repoUser = $this->getDoctrine()->getRepository(User::class);

        /** @var User|null $user */
        $user = $repoUser->findOneBy([
            'email' => $this->getUser()->getUserIdentifier()
        ]);


        $chantiers = $user->getChantiers();

        return $this->render('btp/maitreouvrage/dashboard_mo.html.twig',[
            'chantiers' => $chantiers,
        ]);
    }

    /**
     * @Route("/maitreouvrage/chantier/{id}", name="maitreouvrage_consult_chantier")
     */
    public function consultMo($id): Response
    {
        $repo = $this->getDoctrine()->getRepository(Chantier::class);

        $chantier = $repo->find($id);

        return $this->render('btp/maitreouvrage/consult_chantier.html.twig',[
            'chantier' => $chantier,
            'phases' => $chantier->getPhases(),
        ]);
    }

    /**
     * @Route("/presta/validateTask/{id}", name="validate_task")
     */
    public function validateTask(Tache $tache): Response
    {
        $repo = $this->getDoctrine()->getRepository(Tache::class);
        $repo->updateDureeReelle($tache);

        return $this->redirectToRoute('dashboard_prestataire');
    }


//    #[Route("/blog/articles/12", name:"blog_show")]
//    public function show(): Response
//    {
//        return $this->render('blog/show.html.twig');
//    }
}
