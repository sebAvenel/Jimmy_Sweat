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
        } elseif ($user->getRole() == 'user'){
            $user->setRole('admin');
        }

        $this->manager->flush();
        return $this->redirectToRoute('app_admin_user');
    }

    /**
     * @Route("/delete/{id}", methods="DELETE")
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

        return $this->errorViewDisplay('Ce lien semble périmé');
    }

    /**
     * @Route("/login")
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
     * @Route("/logout", methods={"GET"})
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

        return $this->errorViewDisplay('Ce lien semble périmé');
    }

    /**
     * @Route("/edit/{id}")
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
                echo $avatars_directory;
                if ($avatarFile != null){
                    $this->filesystem->remove($avatars_directory, $avatarFile);
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
