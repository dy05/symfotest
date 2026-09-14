<?php

namespace App\Controller;

use App\Data\ContactData;
use App\Form\ContactType;
use App\Service\ContactService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\ConstraintViolationInterface;

final class ContactController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/contact_back', name: 'contact_back')]
    public function index(Request $request, ContactService $service): Response
    {
        $messages = [];
        $data = new ContactData();
        $form = $this->createForm(ContactType::class, $data);
        if ($request->isXmlHttpRequest()) {
            $json = json_decode($request->getContent(), true);
            $data->email = $json['email'];
            $data->phone = $json['phone'];
            $data->rgpd = true;
            $data->message = $json['message'];
            $data->name = $json['name'];
            $form->submit($json);
        }

        // $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->handleForm($data);
            return new JsonResponse([]);
        } elseif ($form->isSubmitted()) {
            /** @var FormError[] $error */
            $errors = $form->getErrors(true);
            foreach ($errors as $error) {
                $violation = $error->getCause();
                $path = str_replace('data.', '', $violation->getPropertyPath());
                $messages[$path] = $violation->getMessage();
            }

            return new JsonResponse(['errors' => $messages], 400);
        }

        return $this->render('page/contact_back.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, ContactService $service): Response
    {
        $data = new ContactData();
        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->handleForm($data);
            $this->addFlash(
                'success',
                'Your mail successfully sent.'
            );

            return $this->redirectToRoute('contact');

        } elseif ($form->isSubmitted() && !empty($form->getErrors(false, false))) {
            $this->addFlash('danger', 'Please check the form below and try again.');
        }

        return $this->render('page/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/contact_ajax', name: 'app_contact_post_ajax')]
    public function validateAjax(Request $request, ContactService $service): Response
    {
        $data = new ContactData();
        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);
        $messages = [];

        if ($request->isXmlHttpRequest()) {
            $json = json_decode($request->getContent(), true);
            $data->rgpd = true;
            $data->email = $json['email'];
            $data->message = $json['message'];
            $data->phone = $json['phone'];
            $data->name = $json['name'];
            $form->submit($json);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $service->handleForm($data);
            return new JsonResponse([]);
        } elseif ($form->isSubmitted()) {
            /** @var FormError[] $errors */
            $errors = $form->getErrors(true);
            foreach ($errors as $error) {
                /** @var ConstraintViolationInterface $violation */
                $violation = $error->getCause();
                $path = str_replace('data.', '', $violation->getPropertyPath());
                $messages[$path] = $violation->getMessage();
            }

            return new JsonResponse([
                'errors' => $messages
            ], 400);
        }

        return $this->render('page/contact.html.twig', [
            'form' => $form->createView(),
            'messages' => $messages,
        ]);
    }
}
