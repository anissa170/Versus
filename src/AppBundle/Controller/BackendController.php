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
            $em = $this->getDoctrine()->getManager();
            $carteName = $request->request->get('name');
            $cartePoints = json_decode($request->request->get('obj')[0]);

            $carte = new Carte();
            $carte->setNom($carteName);

            if (!empty($_FILES['image']['name'])) {
                $dossier = 'assets/img/uploaded/';
                $fichier = basename($_FILES['image']['name']);
                $taille_maxi = 1000000;
                $taille = filesize($_FILES['image']['tmp_name']);
                $extensions = array('.png', '.gif', '.jpg', '.jpeg');
                $extension = strrchr($_FILES['image']['name'], '.');
                $nameFile = "imgCarte_" . $carte->getId() . "_" . $fichier;
                //Début des vérifications de sécurité...
                if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
                {
                    $erreur = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg, txt ou doc...';
                }
                if($taille>$taille_maxi)
                {
                    $erreur = 'Le fichier est trop gros...';
                }
                if(!isset($erreur)) //S'il n'y a pas d'erreur, on upload
                {
                    //On formate le nom du fichier ici...
                    $fichier = strtr($fichier,
                        'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
                        'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
                    $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
                    if(move_uploaded_file($_FILES['image']['tmp_name'], $dossier . $nameFile)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
                    {
                        echo 'Upload effectué avec succès !';
                    }
                    else //Sinon (la fonction renvoie FALSE).
                    {
                        echo 'Echec de l\'upload !';
                    }
                }

                $carte->setImage($nameFile);
            }

            $em->persist($carte);
            if (!empty($cartePoints)) {
                foreach ($cartePoints as $key => $localisationObject) {
                    $localisation = new Localisation();
                    $localisation->setLabel($localisationObject->name);
                    $localisation->setCarte($carte);
                    $em->persist($localisation);
                    foreach ($localisationObject->points as $key => $pointObject) {
                        $point = new Point();
                        $point->setPosX($pointObject->x);
                        $point->setPosY($pointObject->y);
                        $point->setLocalisation($localisation);
                        $em->persist($point);
                        $localisation->addPoint($point);
                    }
                    $em->persist($localisation);
                    $carte->addLocalisation($localisation);
                }
            }
            $em->persist($carte);
            $em->flush();


            return $this->redirectToRoute('cartesBackend');
        }
        return $this->render('backend/add-carte.html.twig', [

        ]);
    }

    /**
     * @Route("/supprimerCarte/{id}", name="deleteCarte")
     */
    public function deleteCarteAction(Request $request, Carte $carte)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($carte);
        $em->flush();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("sondagesBackend");
        }

        return $this->redirectToRoute("homepage");
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