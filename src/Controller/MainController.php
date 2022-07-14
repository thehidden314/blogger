<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_login')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/inscription', name: 'app_inscription')]
    public function signin(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $encoder, FileUploader $servicePhoto): Response
    {
        $data = $request->request->all();
        $file = $request->files->get('photo');
        if($data){
            //dd($file);

            $user = new User();
            $user->setPrenom($data['prenom'])
                ->setNom($data['nom'])
                ->setEmail($data['email'])
                ->setPassword($encoder->hashPassword($user, $data['password']))
                ->setRoles(['ROLE_ADMIN']);

            if ($file) {
                $user->setPhoto($servicePhoto->upload($file));
            }

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_inscription', [], Response::HTTP_SEE_OTHER);
        }
        
        
        return $this->render('main/inscription.html.twig', [
            
        ]);
    }
}
