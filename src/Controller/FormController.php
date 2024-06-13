<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Form\ContactFormType;

class FormController extends AbstractController
{
    #[Route('/form', name: 'form')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        // Create and handle the form
        $form = $this->createForm(ContactFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get data from form
            $formData = $form->getData();

            // Handle file upload
            $file = $formData['fichier'];
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('files_directory'),
                $fileName
            );

            // Send email with attachment
            $email = (new Email())
                ->from('your@example.com')
                ->to($formData['email'])
                ->subject('Form Submission')
                ->text('Sending emails is fun again!')
                ->html('<p>See Twig integration for better HTML integration!</p>')
                ->attachFromPath($this->getParameter('files_directory') . '/' . $fileName);

            $mailer->send($email);

            // Add a flash message
            $this->addFlash('success', 'Email sent successfully!');

            // Redirect to the same form to show the success message
            return $this->redirectToRoute('form');
        }

        // Render the form
        return $this->render('form/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
