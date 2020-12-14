<?php

namespace App\Controller;

use App\Form\ProfileType;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="symfony_tests")
     */
    public function index(): Response
    {
        return $this->render('symfony_tests/index.html.twig', [

        ]);
    }

    /**
     * @Route("/dashboard", name="dashboard")
     * @param Request $request
     * @param FileUploader $uploader
     * @return Response
     */
    public function dashboard(Request $request, FileUploader $uploader): Response
    {
        $profileForm = $this->createForm(ProfileType::class,$this->getUser());

        $profileForm->handleRequest($request);

        if($profileForm->isSubmitted()){
            $profile = $profileForm->getData();
            $picture = $uploader->upload($profileForm->get('profilePicture')->getData());
            $entityManager = $this->getDoctrine()->getManager();
            $profile->setProfilePicture($picture);
            $entityManager->persist($profile);
            $entityManager->flush();
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('symfony_tests/dashboard.html.twig', [
            'profileForm'=>$profileForm->createView()
        ]);
    }
}
