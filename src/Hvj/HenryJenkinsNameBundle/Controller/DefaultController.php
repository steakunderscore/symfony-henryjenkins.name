<?php

namespace Hvj\HenryJenkinsNameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hvj\HenryJenkinsNameBundle\Entity\Enquiry;
use Hvj\HenryJenkinsNameBundle\Form\EnquiryType;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->aboutAction();
    }

    public function aboutAction()
    {
        return $this->render('HvjHenryJenkinsNameBundle:Page:about.html.twig');
    }

    public function contactAction()
    {
        $enquiry = new Enquiry();
        $form = $this->createForm(new EnquiryType(), $enquiry);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Contact enquiry form henryjenkins.name')
                    ->setFrom('symfony@henryjenkins.name')
                    ->setTo($this->container->getParameter('hvj_henry_jenkins_name.emails.contact_email'))
                    ->setBody($this->renderView('HvjHenryJenkinsNameBundle:Email:contact.txt.twig', array('enquiry' => $enquiry)));
                $this->get('mailer')->send($message);

                $this->get('session')->getFlashBag()->add('notice', 'Your contact enquiry was successfully sent. Thank you!');

                // Redirect - This is important to prevent users re-posting
                // the form if they refresh the page
                return $this->redirect($this->generateUrl('hvj_henry_jenkins_name_contact'));
            }
        }

        return $this->render('HvjHenryJenkinsNameBundle:Page:contact.html.twig', array(
            'form' => $form->createView()
        ));
    }

}
