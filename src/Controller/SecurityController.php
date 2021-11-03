<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Services\NotificationService;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route("/register", name: "app_register")]
    public function register(Request $request, TranslatorInterface $translator, UserPasswordEncoderInterface $encoder, NotificationService $notificationService): RedirectResponse|Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user->setEnabled(true);
            $user->setRoles(["ROLE_USER"]);
            $user->setPassword($encoder->encodePassword($user, $form->get("password")->getData()));
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", $translator->trans("registration.success", [], "FlashesMessages"));
            $notifContent = $translator->trans("notification.user.add", [], "BlogTrans");
            $notification = $notificationService->notify($notifContent, $user);
            $em->persist($notification);
            $em->flush();
            return $this->redirectToRoute("home");
        }
        return $this->render("security/register.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new LogicException('Cette méthode peut être vide - elle sera interceptée par la clé de déconnexion de votre pare-feu.');
    }
}
