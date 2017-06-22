<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Proposition;
use AppBundle\Entity\Sondage;
use AppBundle\Entity\Reponse;
use AppBundle\Form\SondageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SondageController extends Controller
{
    /**
     * @Route("/sondages", name="afficherSondages")
     */
    public function sondagesAction(Request $request)
    {
        $sondages = $this
            ->getDoctrine()
            ->getRepository("AppBundle:Sondage")
            ->findAll();

        return $this->render('default/sondage.html.twig', [
            'sondages' => $sondages
        ]);
    }

    /**
     * @Route("/sondage/{id}/answer", name="reponse")
     */
    public function answerAction(Request $request, $id)
    {	
    	$session = $request->getSession();

    	if ($request->getMethod() == 'POST') {
    		if (empty($request->request->get('answer_id'))) {
    			$session->getFlashBag()->add(
				    'error',
				    "Un probleme est survenue lors de l'envoie de votre reponse."
				);
    		}
    		if (empty($request->request->get('zone_id'))) {
    			$session->getFlashBag()->add(
				    'error',
				    "Un probleme est survenue lors de l'envoie de votre zone."
				);
    		}
    	}

    	if ($request->getMethod() == 'POST' && !$session->getFlashBag()->has('error')) {
    		$em = $this->getDoctrine()->getManager();
    		$reponse = new Reponse();
    		$reponse->setDatetime(new \DateTime());
    		$reponse->setProposition($this->getDoctrine()->getRepository("AppBundle:Proposition")->find($request->request->get('answer_id')));
    		$reponse->setLocalisation($this->getDoctrine()->getRepository("AppBundle:Localisation")->find($request->request->get('zone_id')));
    		$em->persist($reponse);
    		$em->flush();
    		return $this->render('sondage/answer-validate.html.twig', [
	        ]);
    	}
    	else {
	    	$sondage = $this->getDoctrine()->getRepository("AppBundle:Sondage")->find($id);
	    	$carte = $sondage->getCarte();
	    	$localisations = $carte->getLocalisations();
	    	$reponses = $sondage->getPropositions();
	    	$imageCarte = $carte->getImage();
	        return $this->render('sondage/answer.html.twig', [
	            'image_carte' => $imageCarte,
	            'localisations' => $localisations,
	            'id' => $id,
	            'reponses' => $reponses
	        ]);
    	}
    }

	/**
     * @Route("/sondage/{id}", name="afficherSondage")
     */
    public function sondageAction(Request $request, Sondage $sondage)
    {
        return $this->render('default/sondage.html.twig', [
            'sondage' => $sondage
        ]);
    }

    /**
     * @Route("/addSondage", name="ajouterSondage")
     */
    public function addSondageAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        if ($request->getMethod() == 'POST') {
            $sondage = new Sondage();
            $sondage->setTitre($request->request->get('titre'));
            $sondage->setAuteur($this->getUser());
            $sondage->setCreationDate(new \DateTime());
            $sondage->setPublier(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($sondage);

            if ($_POST['proposition_1']) {
                $proposition_1 = new Proposition();
                $proposition_1->setLabel($_POST['proposition_1']);
                $proposition_1->setCouleur($_POST['proposition_1_color']);
                $proposition_1->setSondage($sondage);
                $sondage->addProposition($proposition_1);
            }

            if ($_POST['proposition_2']) {
                $proposition_2 = new Proposition();
                $proposition_2->setLabel($_POST['proposition_2']);
                $proposition_2->setCouleur($_POST['proposition_2_color']);
                $proposition_2->setSondage($sondage);
                $sondage->addProposition($proposition_2);
            }

            if ($_POST['proposition_3']) {
                $proposition_3 = new Proposition();
                $proposition_3->setLabel($_POST['proposition_3']);
                $proposition_3->setCouleur($_POST['proposition_3_color']);
                $proposition_3->setSondage($sondage);
                $sondage->addProposition($proposition_3);
            }

            if ($_POST['proposition_4']) {
                $proposition_4 = new Proposition();
                $proposition_4->setLabel($_POST['proposition_4']);
                $proposition_4->setCouleur($_POST['proposition_4_color']);
                $proposition_4->setSondage($sondage);
                $sondage->addProposition($proposition_4);
            }

            if ($_POST['proposition_5']) {
                $proposition_5 = new Proposition();
                $proposition_5->setLabel($_POST['proposition_5']);
                $proposition_5->setCouleur($_POST['proposition_5_color']);
                $proposition_5->setSondage($sondage);
                $sondage->addProposition($proposition_5);
            }

            $em->persist($sondage);
            $em->flush();

            if (!empty($_FILES['image']['name'])) {
                $dossier = 'assets/img/uploaded/';
                $fichier = basename($_FILES['image']['name']);
                $taille_maxi = 1000000;
                $taille = filesize($_FILES['image']['tmp_name']);
                $extensions = array('.png', '.gif', '.jpg', '.jpeg');
                $extension = strrchr($_FILES['image']['name'], '.');
                $nameFile = "imgSondage_" . $sondage->getId() . "_" . $fichier;
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

                $sondage->setImage($nameFile);
            }

            $carte = $this->getDoctrine()->getRepository('AppBundle:Carte')->find($_POST['carte']);
            $sondage->setCarte($carte);

            $em->persist($sondage);
            $em->flush();
        }

        $cartes = $this->getDoctrine()->getRepository("AppBundle:Carte")->findAll();

        return $this->render("sondage/new-sondage.html.twig", [
            'cartes' => $cartes
        ]);
    }

    /**
     * @Route("/editSondage/{id}", name="editerSondage")
     */
    public function editSondageAction(Request $request, Sondage $sondage)
    {
        if ($sondage->getAuteur() != $this->getUser()) {
            return $this->redirectToRoute("homepage");
        }
        $em = $this->getDoctrine()->getManager();

        $sondage->setTitre("Wolololo");

        $em->flush();

        return $this->redirectToRoute("homepage");
    }

    /**
     * @Route("/deleteSondage/{id}", name="supprimerSondage")
     */
    public function deleteSondageAction(Request $request, Sondage $sondage)
    {
        if ($sondage->getAuteur() != $this->getUser() || !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("homepage");
        }
        $em = $this->getDoctrine()->getManager();

        $em->remove($sondage);
        $em->flush();

        return $this->redirectToRoute("homepage");
    }
}
