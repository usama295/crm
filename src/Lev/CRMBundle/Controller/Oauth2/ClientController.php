<?php

namespace App\Lev\CRMBundle\Controller\Oauth2;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Lev\CRMBundle\Entity\Oauth2\Client;
use App\Lev\CRMBundle\Form\Oauth2\ClientType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Oauth2\Client controller.
 *
 */
class ClientController extends Controller
{

    /**
     * Lists all Oauth2\Client entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LevCRMBundle:Oauth2\Client')->findAll();

        return $this->render('LevCRMBundle:Oauth2/Client:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Oauth2\Client entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Client();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('client'));
        }

        return $this->render('LevCRMBundle:Oauth2/Client:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Oauth2\Client entity.
     *
     * @param Client $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Client $entity)
    {
        
        $form = $this->createForm(ClientType::class, $entity, array(
            'action' => $this->generateUrl('client_create'),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, array( 'label' => 'Create', 'attr' => ['class' => 'btn-primary']));

        return $form;
    }

    /**
     * Displays a form to create a new Oauth2\Client entity.
     *
     */
    public function newAction()
    {
        $entity = new Client();
        $form   = $this->createCreateForm($entity);

        return $this->render('LevCRMBundle:Oauth2/Client:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Oauth2\Client entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LevCRMBundle:Oauth2\Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Oauth2\Client entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LevCRMBundle:Oauth2/Client:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Oauth2\Client entity.
    *
    * @param Client $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Client $entity)
    {
        $form = $this->createForm(ClientType::class, $entity, array(
            'action' => $this->generateUrl('client_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', SubmitType::class, array( 'label' => 'Update', 'attr' => ['class' => 'btn-primary']));

        return $form;
    }
    /**
     * Edits an existing Oauth2\Client entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LevCRMBundle:Oauth2\Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Oauth2\Client entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('client'));
        }

        return $this->render('LevCRMBundle:Oauth2/Client:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Oauth2\Client entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LevCRMBundle:Oauth2\Client')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Oauth2\Client entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('client'));
    }

    /**
     * Creates a form to delete a Oauth2\Client entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('client_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array( 'label' => 'Delete', 'attr' => ['class' => 'btn-danger']))
            ->getForm()
        ;
    }
}
