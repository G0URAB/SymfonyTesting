<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class DashboardController extends AbstractController
{

    private $security;

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
        $profileForm = $this->createForm(ProfileType::class, $this->getUser());

        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $profile = $profileForm->getData();
            $picture = $profileForm->get('profilePicture')->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $picture = $uploader->upload($picture);
            $profile->setProfilePicture($picture);

            $entityManager->persist($profile);
            $entityManager->flush();
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('symfony_tests/dashboard.html.twig', [
            'profileForm' => $profileForm->createView()
        ]);
    }

    /**
     * @Route("/delete/picture/{id}", name="delete_picture")
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function deletePicture(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if (file_exists($this->getParameter('profile_picture_directory') . '/' . $user->getProfilePicture())) {
                unlink($this->getParameter('profile_picture_directory') . '/' . $user->getProfilePicture());
        }
        $user->setProfilePicture(null);
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute("dashboard");
    }

    /**
     * @Route("/whoami", name="whoami")
     * @param Request $request
     * @param Security $security
     * @return mixed
     */
    public function getMyDetails(Request $request, Security $security)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->find($id);
            if ($id && $security->isGranted('ROLE_USER')) {
                return new JsonResponse([
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                    'gender' => $user->getGender(),
                    'age' => $user->getAge()
                ], 200);
            } else
                return new JsonResponse(['error' => 'Please login to use this API'], 401);
        } else
            return new Response("Invalid request", 401);
    }
}
