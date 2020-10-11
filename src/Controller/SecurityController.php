<?php

namespace App\Controller;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Kontroler pro přihlašování uživatelů.
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * Vykresluje přihlašovací formulář.
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     * @Route("/prihlaseni", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY'))
            return $this->redirectToRoute('administration');

        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername]);
    }

    /**
     * Vytváří a zpracovává formulář pro registraci uživatele.
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @return Response
     * @throws ORMException Jestliže nastane chyba při ukládání uživatele.
     * @Route("/registrace", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository): Response
    {
        $user = new User();
        $registerForm = $this->createFormBuilder($user)
            ->add('username', null, ['label' => 'Jméno'])
            ->add('password', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Hesla nesouhlasí.',
                'first_options' => ['label' => 'Heslo'],
                'second_options' => ['label' => 'Heslo znovu']
            ])
            ->add('y', TextType::class, [
                'label' => 'Zadejte aktuální rok (antispam)',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Pole pro aktuální rok nemůže být prázdné!']),
                    new EqualTo(['value' => date("Y"), 'message' => 'Chybně vyplněný antispam!'])
                ]
            ])
            ->add('submit', SubmitType::class, ['label' => 'Registrovat'])
            ->getForm();

        $registerForm->handleRequest($request);
        if ($registerForm->isSubmitted() && $registerForm->isValid()) {

            // Šifrování uživatelského hesla před uložením do db
            $password = $passwordEncoder->encodePassword($user, $registerForm->get('password')->getData());
            $user->setPassword($password);

            $userRepository->save($user);

            $this->addFlash('notice', 'Nyní se můžete přihlásit.');

            return $this->redirectToRoute('login');
        }

        return $this->render('security/register.html.twig', ['registerForm' => $registerForm->createView()]);
    }
    
}
