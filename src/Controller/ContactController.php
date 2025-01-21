<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('name');
            $email = $request->request->get('email');
            $message = $request->request->get('message');

            if (!empty($nom) && !empty($email) && !empty($message)) {
                $emailMessage = (new Email())
                    ->from($email)
                    ->to('ton-email@exemple.com')
                    ->subject('Nouveau message de contact')
                    ->text("Nom : $nom\nEmail : $email\nMessage :\n$message");

                $mailer->send($emailMessage);

                $this->addFlash('success', 'Votre message a bien été envoyé !');
            } else {
                $this->addFlash('error', 'Veuillez remplir tous les champs.');
            }
        }

        return $this->render('contact/index.html.twig');
    }
}
