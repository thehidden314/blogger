<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\FileUploader;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(UserRepository $userRepo, FileUploader $service): Response
    {
        $users = $userRepo->findAll();
        $datas = [];
        foreach($users as $user){
            $user->setPhoto($service->getUrl($user->getPhoto()));
           
            $datas[] = $user;
        }
        return $this->render('main/index.html.twig', [
            'users' => $datas,
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

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
        
        
        return $this->render('main/inscription.html.twig', [
            
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('main/login.html.twig', [
            
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(): Response
    {
        
    }
}
