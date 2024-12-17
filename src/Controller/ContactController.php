<?php

namespace App\Controller;

use App\Entity\UserContact;
use App\Form\ContactFormType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, EntityManagerInterface $entityManager,  MailerInterface $mailer, LoggerInterface $logger): Response
    {
        $form = $this->createForm(ContactFormType::class, new UserContact());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserContact $userContact */
            $userContact = $form->getData();

            $entityManager->persist($userContact);
            $entityManager->flush();

            try {
                $email = (new TemplatedEmail())
                    ->to($userContact->getService())
                    ->from($userContact->getEmail())
                    ->subject('demande de contact')
                    ->htmlTemplate('emails/contact.html.twig')
                    ->context(['data' => $userContact]);

                $mailer->send($email);
                $this->addFlash('success', 'le Message à bien été envoyé');
            } catch (TransportExceptionInterface $transportException) {
                $logger->error('TransportException: '.$transportException->getMessage());
                $this->addFlash('danger', 'le Message n\'a pas été envoyé');
            } catch (\Exception $exception) {
                $logger->error('Exception: '.$exception->getMessage());
                $this->addFlash('danger', 'le Message n\'a pas été envoyé');
            }

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}
