<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserLoginType;
use App\Form\UserRegistrationType;
use App\Form\UserForgotPasswordType;
use App\Form\UserUpdatePasswordType;
use App\Notification\RegistrationNotification;
use App\Notification\UpdatePasswordNotification;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(UserRepository $userRepository, ObjectManager $manager)
    {
        $this->userRepository = $userRepository;
        $this->manager = $manager;
    }

    /**
     * @Route("/user/update/{id}", name="update_user_role")
     * @param $id
     * @return RedirectResponse
     */
    public function updateRole($id): RedirectResponse
    {
        $user = $this->userRepository->find($id);
        if ($user->getRole() == 'admin'){
            $user->setRole('user');
        }

        if ($user->getRole() == 'user'){
            $user->setRole('admin');
        }

        $this->manager->flush();
        return $this->redirectToRoute('user_admin');
    }

    /**
     * @Route("/user/delete/{id}", name="delete_user", methods="DELETE")
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteFromAdmin($id, Request $request): RedirectResponse
    {
        $user = $this->userRepository->find($id);
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->get('_token'))){
            $this->manager->remove($user);
            $this->manager->flush();
            $this->addFlash('success', 'L\'utilisateur ' . $user->getName() . ' a bien été supprimé');
            return $this->redirectToRoute('user_admin');
        }

        $this->addFlash('failure', 'L\' utilisateur ' . $user->getName() . ' n\'a pas pu être supprimé');
        return $this->redirectToRoute('user_admin');
    }

    /**
     * @Route("/user/registration", name="user_registration")
     * @param Request $request
     * @param RegistrationNotification $notification
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function register(Request $request, RegistrationNotification $notification, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $this->manager->persist($user);
            $this->manager->flush();
            $notification->notify($user);
            return $this->redirectToRoute('user_login');
        }

        return $this->render('user/userRegistration.html.twig', [
           'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/confirmation_registration/{token}", name="user_confirmation_registration")
     * @param $token
     * @return Response
     */
    public function confirmRegistration(string $token): Response
    {
        if ($this->userRepository->findOneBy(['token' => $token])){
            $user = $this->userRepository->findOneBy(['token' => $token]);
            $user->setRole('user');
            $user->setActivated(1);
            $user->setToken(md5(uniqid('jimmySweat', true)));
            $this->manager->persist($user);
            $this->manager->flush();
            return $this->redirectToRoute('user_login');
        }

        return $this->errorViewDisplay('Ce lien semble périmé');
    }

    /**
     * @Route("/login", name="user_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(UserLoginType::class);
        return $this->render('user/userLogin.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername = $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("user/logout", name="user_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * @Route("user/forgot_password/", name="forgot_password")
     * @param Request $request
     * @param UpdatePasswordNotification $notification
     * @return Response
     */
    public function forgotPassword(Request $request, UpdatePasswordNotification $notification): Response
    {
        $form = $this->createForm(UserForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $user = $this->userRepository->findOneBy(['email' => $form->get('email')->getData()]);
            if ($user != null){
                $notification->notify($user);
                return $this->render('user/userForgotPassword.html.twig', [
                    'email' => $form->get('email')->getData()
                ]);
            }

            return $this->render('user/userForgotPassword.html.twig', [
                'email' => $form->get('email')->getData()
            ]);
        }

        return $this->render('user/userForgotPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/update_password/{token}", name="update_password")
     * @param string $token
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function updatePassword(string $token, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        if ($this->userRepository->findOneBy(['token' => $token])){
            $user = $this->userRepository->findOneBy(['token' => $token]);
            $form = $this->createForm(UserUpdatePasswordType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $user->setPassword($encoder->encodePassword($user, $form->get('password')->getData()));
                $user->setToken(md5(uniqid('jimmySweat', true)));
                $this->manager->persist($user);
                $this->manager->flush();
                return $this->render('user/userUpdatePassword.html.twig', [
                    'user' => $user
                ]);
            }

            return $this->render('user/userUpdatePassword.html.twig', [
                'form' => $form->createView()
            ]);
        }

        return $this->errorViewDisplay('Ce lien semble périmé');
    }
}