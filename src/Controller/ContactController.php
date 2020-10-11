<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swift_Mailer;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Kontroler pro kontaktní formulář.
 * @package App\Controller
 */
class ContactController extends AbstractController
{
    /**
     * Vytváří a zpracovává kontaktní formulář.
     * @param Swift_Mailer $mailer
     * @param Request $request
     * @return Response
     * @Route("/kontakt", name="contact")
     */
    public function index(Swift_Mailer $mailer, Request $request): Response
    {
        $contactMessage = new ContactMessage();
        $contactForm = $this->createFormBuilder($contactMessage)
            ->add('email', null, ['label' => 'Vaše emailová adresa'])
            ->add('y', TextType::class, [
                'label' => 'Zadejte aktuální rok',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Pole pro aktuální rok nemůže být prázdné!']),
                    new EqualTo(['value' => date("Y"), 'message' => 'Chybně vyplněný antispam!'])
                ]
            ])
            ->add('message', TextareaType::class, ['label' => 'Zpráva'])
            ->add('submit', SubmitType::class, ['label' => 'Odeslat'])
            ->getForm();

        // Zpracování formuláře
        $contactForm->handleRequest($request);
        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $mailer->send((new \Swift_Message(
                'Email z webu',
                $contactMessage->getMessage(),
                'text/plain'
            ))->setFrom($contactMessage->getEmail()));

            $this->addFlash('notice', 'Email byl úspěšně odeslán.');
            return $this->redirectToRoute('contact');
        }
        return $this->render('contact/index.html.twig', [
            'contactForm' => $contactForm->createView()
        ]);
    }
}
