<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        if ($session->isStarted()) {
            return $this->redirectToRoute('fos_user_profile_show');
        }

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/lock/{username}", name="lock_user")
     */
    public function lockUserAction(Request $request, $username)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        if ($user->isEnabled()) {
            $user->setEnabled(0);
        } else {
            $user->setEnabled(1);
        }

        $userManager->updateUser($user);
        return $this->redirectToRoute('fos_user_profile_show');
    }

    /**
     * @Route("/newUser", name="new_user")
     */
    public function newUserAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $form = $this->createFormBuilder()
            ->add('username', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create user'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUsername($form->getData()['username']);
            $user->setEnabled(true);
            $user->setEmail("qwert@gmail.com");
            $user->setPlainPassword('1111');

            $userManager->updatePassword($user);
            $userManager->updateUser($user);
            return $this->redirectToRoute('fos_user_profile_show');
        }
        return $this->render('create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
