<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("", name="app_account", methods="GET")
     * @IsGranted("ROLE_USER")
     */
    public function show(): Response
    {
        // if(! $this->getUser()){
        //     $this->addFlash('error', 'You Need To Login In First!');
        //     return $this->redirectToRoute('app_login');
        // }
        // $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('account/show.html.twig');
    }

    /**
     * @Route("/edit", name="app_account_edit", methods={"GET" ,"POST"})
    * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        // if(! $this->getUser()){
        //     $this->addFlash('error', 'You Need To Login In First!');
        //     return $this->redirectToRoute('app_login');
        // }
        // $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->flush();
            $this->addFlash('success', 'Account updated successefully');

            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/change-password", name="app_account_change_password", methods={"GET" ,"POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function changePassword(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        // if(! $this->getUser()){
        //     $this->addFlash('error', 'You Need To Login In First!');
        //     return $this->redirectToRoute('app_login');
        // }
        // $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class, null, [
            'current_password_is_required' => true
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword($passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData()));
            $em->flush();

            $this->addFlash('success', 'Password Updated Successefully');
            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/change-password.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
