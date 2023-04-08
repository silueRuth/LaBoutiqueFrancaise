<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        //creation de l'objet User pour la recuperation des données
        $user = new User();

        //Creation du formulaire lier a la classe registerType et a l'entité User
        $form = $this->createForm(RegisterType::class, $user);

        // le formulaire à la possibilité d'ecouter la requete car l'objet request est donné a la variable form 
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //mettre les données saisir par l'utilisateur dans la variable user
            $user = $form->getData();

            // encoder le mot de passe de l'utilisateur
            $Password = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($Password);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            //$doctrine = $this->getDoctrine()->getManager();
        }


        // dd($user);

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
