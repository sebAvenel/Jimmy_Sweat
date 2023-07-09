<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserLoginType;
use App\Form\UserRegistrationType;
use App\Form\UserForgotPasswordType;
use App\Form\UserType;
use App\Form\UserUpdatePasswordType;
use App\Notification\RegistrationNotification;
use App\Notification\UpdatePasswordNotification;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 */
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
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * UserController constructor.
     * @param UserRepository $userRepository
     * @param ObjectManager $manager
     * @param Filesystem $filesystem
     */
    public function __construct(UserRepository $userRepository, ObjectManager $manager, Filesystem $filesystem)
    {
        $this->userRepository = $userRepository;
        $this->manager = $manager;
        $this->filesystem = $filesystem;
    }

    /**
     * @Route("update/{id}")
     * @param User $user
     * @return RedirectResponse
     */
    public function updateRole(User $user): RedirectResponse
    {
        if ($user->getRole() == 'admin'){
            $user->setRole('user');
            $this->addFlash('userUpdateRoleUser', 'L\'utilisateur ' . $user->getName(). ' a maintenant le role utilisateur');
        } elseif ($user->getRole() == 'user'){
            $user->setRole('admin');
            $this->addFlash('userUpdateRoleAdmin', 'L\'utilisateur ' . $user->getName(). ' a maintenant le role administrateur');
        }

        $this->manager->flush();
        return $this->redirectToRoute('app_admin_user');
    }

    /**
     * @Route("/delete/{id}", methods="DELETE")
     * @param User $user
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteFromAdmin(User $user, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->get('_token'))){
            $this->manager->remove($user);
            $this->manager->flush();
            $this->addFlash('success', 'L\'utilisateur ' . $user->getName() . ' a bien été supprimé');
            return $this->redirectToRoute('app_admin_user');
        }

        $this->addFlash('failure', 'L\' utilisateur ' . $user->getName() . ' n\'a pas pu être supprimé');
        return $this->redirectToRoute('app_admin_user');
    }

    /**
     * @Route("/registration")
     * @Template()
     * @param Request $request
     * @param RegistrationNotification $notification
     * @param UserPasswordEncoderInterface $encoder
     * @return array
     */
    public function registration(Request $request, RegistrationNotification $notification, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $this->manager->persist($user);
            $this->manager->flush();
            $notification->notify($user);
            return ['email' => $user->getEmail()];
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/confirmation_registration/{token}")
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

            return $this->redirectToRoute('app_user_login');
        }

        $this->addFlash('failureConfirmRegistration', 'Vous avez déjà valider votre inscription, merci de vous authentifier');
        return $this->redirectToRoute('app_home_home');
    }

    /**
     * @Route("/login", name="app_user_login")
     * @Template()
     * @param AuthenticationUtils $authenticationUtils
     * @return array
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $form = $this->createForm(UserLoginType::class);
        return [
            'form' => $form->createView(),
            'last_username' => $lastUsername = $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ];
    }

    /**
     * @Route("/logout", name="app_user_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * @Route("/forgot_password")
     * @Template()
     * @param Request $request
     * @param UpdatePasswordNotification $notification
     * @return array
     */
    public function forgotPassword(Request $request, UpdatePasswordNotification $notification)
    {
        $form = $this->createForm(UserForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $user = $this->userRepository->findOneBy(['email' => $form->get('email')->getData()]);
            if ($user != null){
                $notification->notify($user);
                return ['email' => $form->get('email')->getData()];
            }

            return ['email' => $form->get('email')->getData()];
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/update_password/{token}")
     * @Template()
     * @param string $token
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return array|Response
     */
    public function updatePassword(string $token, Request $request, UserPasswordEncoderInterface $encoder)
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
                return ['user' => $user];
            }

            return ['form' => $form->createView()];
        }

        $this->addFlash('failureUpdatePassword', 'Ce lien a déjà été utilisé, veuillez renouveler votre demande de nouveau mot de passe');
        return $this->redirectToRoute('app_home_home');
    }

    /**
     * @Route("/edit/{slug}/{id}")
     * @Template()
     * @param User $user
     * @param Request $request
     * @return array
     */
    public function edit(User $user, Request $request)
    {
        $avatarFile = $user->getAvatar();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            if ($user->getAvatar() != null){
                $file = $user->getAvatar();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $avatars_directory = $this->getParameter('avatars_directory');
                if ($avatarFile != null){
                    $this->filesystem->remove($avatars_directory . '/' . $avatarFile);
                }
                $file->move(
                    $avatars_directory,
                    $fileName
                );
                $user->setAvatar($fileName);
            } else {
                $user->setAvatar($avatarFile);
            }
            $this->manager->persist($user);
            $this->manager->flush();

            return $this->render('user/edit.html.twig');
        }

        return ['form' => $form->createView()];
    }
}
