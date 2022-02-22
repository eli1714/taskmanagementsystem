<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Taskmanagement;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class TaskController extends Controller
{
    /**
     * @Route("/", name="task_management")
     */
    public function taskAction(Request $request)
    {   
        $tasks = $this->getDoctrine()
            ->getRepository('AppBundle:Taskmanagement')
            ->findAll();

        return $this->render('task/index.html.twig', array(
            'tasks' => $tasks
        ));
    }

    /**
     * @Route("/task/create", name="task_create")
     */
    public function createAction(Request $request)
    {   
        $task = new Taskmanagement; 

        $form = $this->createFormBuilder($task)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('status', ChoiceType::class, array('choices' => array('Incomplete' => 'Incomplete', 'In progress' => 'In progress', 'Completed' => 'Completed'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Create Task', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //Get Data
            $name = $form['name']->getData();
            $status = $form['status']->getData();

            $task->setName($name);
            $task->setStatus($status);

            $em = $this->getDoctrine()->getManager();

            $em->persist($task);
            $em->flush();

            $this->addFlash(
                'notice',
                'Task Added'
            );

            return $this->redirectToRoute('task_management');
        }

        return $this->render('task/create.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/task/edit/{id}", name="task_edit")
     */
    public function editAction($id, Request $request)
    {   
        $task = $this->getDoctrine()
        ->getRepository('AppBundle:Taskmanagement')
        ->find($id);

            $task->setName($task->getName());
            $task->setStatus($task->getStatus());

        $form = $this->createFormBuilder($task)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('status', ChoiceType::class, array('choices' => array('Incomplete' => 'Incomplete', 'In progress' => 'In progress', 'Completed' => 'Completed'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Update Task', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //Get Data
            $name = $form['name']->getData();
            $status = $form['status']->getData();

            $em = $this->getDoctrine()->getManager();
            $task = $em->getRepository('AppBundle:Taskmanagement')->find($id);

            $task->setName($name);
            $task->setStatus($status);

            
            $em->flush();

            $this->addFlash(
                'notice',
                'Task Updated'
            );

            return $this->redirectToRoute('task_management');
        }

        
        return $this->render('task/edit.html.twig', array(
            'task' => $task,
            'form' => $form->createView()
        ));
    }
    /**
     * @Route("/task/delete/{id}", name="task_delete")
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('AppBundle:Taskmanagement')->find($id);

        $em->remove($task);
        $em->flush();

        $this->addFlash(
            'notice',
            'Task Removed'
        );

        return $this->redirectToRoute('task_management');
    }

}
