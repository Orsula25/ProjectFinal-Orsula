<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


final class TestMailController extends AbstractController
{
    #[Route('/test/mail', name: 'app_test_mail')]
    public function test( MailerInterface $mailer): Response
    {
        $mail = (new Email())
        ->from('no-reply@logix-gstock.test')
        ->to('test@example.com')
        ->subject('Test mailtrap ✔')
        ->html('<p>Ceci est un test Mailtrap depuis Logix G-Stock.</p>');
        
        $mailer->send($mail);
        
        
          return new Response('Email envoyé vers Mailtrap ');
    }
}
