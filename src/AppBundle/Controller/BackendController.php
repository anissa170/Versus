<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Sondage;
use AppBundle\Entity\Carte;
use AppBundle\Entity\Localisation;
use AppBundle\Entity\Point;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/backend")
 * @Security("has_role('ROLE_ADMIN')")
 */
class BackendController extends Controller
{
    /**
     * @Route("/", name="homeBackend")
     */
    public function indexAction()
    {
        $sondages = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Sondage')
            ->findAll();

        $reponses = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Reponse')
            ->findAll();

        $utilisateurs = $this
            ->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();

        return $this->render('backend/index.html.twig', [
            'sondages' => $sondages,
            'reponses' => $reponses,
            'utilisateurs' => $utilisateurs
        ]);
    }

    /**
     * @Route("/sondeurs/{page}", defaults={"page": "1"}, name="sondeursBackend")
     */
    public function sondeursAction(Request $request, $page)
    {
        if ($request->getMethod() == 'POST') {
            $user = $this
                ->getDoctrine()
                ->getRepository("AppBundle:User")
                ->find($request->request->get("idUser"));

            if ($this->getUser() != $user) {
                if ($request->request->get("droit") == "admin") {
                    $user->addRole("ROLE_ADMIN");
                } else {
                    $user->removeRole("ROLE_ADMIN");
                }
            }
        }

        $rawSondeurs = $this
            ->getDoctrine()
            ->getRepository("AppBundle:User");

        $sondeurs = $rawSondeurs
            ->findBy(
                array(), /* search criteria */
                array('id' => 'DESC'), /* order */
                10, // limit
                10 * ($page - 1)
            );

        $pages = ceil(count($rawSondeurs->findAll()) / 10);

        return $this->render('backend/sondeurs.html.twig', [
            'sondeurs' => $sondeurs,
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/sondages/{page}", defaults={"page": "1"}, name="sondagesBackend")
     */
    public function sondagesAction($page)
    {
        $rawSondages = $this
            ->getDoctrine()
            ->getRepository("AppBundle:Sondage");

        $sondages = $rawSondages
            ->findBy(
                array(), /* search criteria */
                array('id' => 'DESC'), /* order */
                10, // limit
                10 * ($page - 1)
            );

        $pages = ceil(count($rawSondages->findAll()) / 10);

        return $this->render('backend/sondages.html.twig', [
            'sondages' => $sondages,
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/cartes/{page}", defaults={"page": "1"}, name="cartesBackend")
     */
    public function cartesAction($page)
    {
        $rawSondages = $this
            ->getDoctrine()
            ->getRepository("AppBundle:Carte");

        $cartes = $rawSondages
            ->findBy(
                array(), /* search criteria */
                array('id' => 'DESC'), /* order */
                10, // limit
                10 * ($page - 1)
            );

        $pages = ceil(count($rawSondages->findAll()) / 10);

        return $this->render('backend/cartes.html.twig', [
            'cartes' => $cartes,
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/cartes/{id}/region", name="regionsFromCarteBackend")
     */
    public function regionsFromCarteAction(Carte $carte)
    {
        return $this->render('backend/regions.html.twig', [
            'carte' => $carte,
            'regions' => $carte->getLocalisations()
        ]);
    }

    /**
     * @Route("/carte/ajouter", name="addCarte")
     */
    public function ajouterCarteAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $carteName = $request->request->get('name');
            $carteImage = $request->request->get('pic');
            $cartePoints = json_decode($request->request->get('obj')[0]);

            $carte = new Carte();
            $carte->setNom($carteName);
            $carte->setImage($carteImage);
            foreach ($cartePoints as $key => $localisationObject) {
                $localisation = new Localisation();
                $localisation->setLabel($localisationObject->name);
                foreach ($localisationObject->points as $key => $pointObject) {
                    $point = new Point();
                    $point->setPosX($pointObject->x);
                    $point->setPosY($pointObject->y);
                    $localisation->addPoint($point);
                }
                $carte->addLocalisation($localisation);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($carte);
            $em->flush();

            return $this->redirectToRoute('cartesBackend');
        }
        return $this->render('backend/add-carte.html.twig', [

        ]);
    }


    /**
     * @Route("/sondage/{id}", name="propositionsFromSondageBackend")
     */
    public function propositionsFromSondageAction(Sondage $sondage)
    {
        return $this->render('backend/propositions.html.twig', [
            'propositions' => $sondage->getPropositions()
        ]);
    }
}