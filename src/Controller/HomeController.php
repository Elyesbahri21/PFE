<?php
namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $address = $data['email'];
            $content = $data['content'];

            $email = (new Email())
                ->from($address)
                ->to('admin@contractlab.com')
                ->subject('Demande de contact')
                ->text($content);

            $mailer->send($email);
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'formulaire' => $form->createView() // Pass FormView to the template
        ]);
    }
}
