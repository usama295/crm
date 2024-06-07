<?php

namespace App\Lev\CRMBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Lev\CRMBundle\Entity\StaffRole;
use App\Lev\CRMBundle\Form\StaffRoleType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * StaffRole controller.
 *
 */
class StaffRoleController extends Controller
{

    /**
     * Lists all StaffRole entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LevCRMBundle:StaffRole')->findAll();

        return $this->render('LevCRMBundle:StaffRole:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new StaffRole entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new StaffRole('', array());
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('staffrole'));
        }

        return $this->render('LevCRMBundle:StaffRole:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a StaffRole entity.
     *
     * @param StaffRole $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(StaffRole $entity)
    {
        $form = $this->createForm(StaffRoleType::class, $entity, array(
            'action' => $this->generateUrl('staffrole_create'),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, 
            array( 'label' => 'Create',
             'attr' => ['class' => 'btn-primary'])
        );

        return $form;
    }

    /**
     * Displays a form to create a new StaffRole entity.
     *
     */
    public function newAction()
    {
        $entity = new StaffRole('', array());
        $form   = $this->createCreateForm($entity);

        return $this->render('LevCRMBundle:StaffRole:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing StaffRole entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LevCRMBundle:StaffRole')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StaffRole entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LevCRMBundle:StaffRole:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a StaffRole entity.
    *
    * @param StaffRole $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(StaffRole $entity)
    {
        $form = $this->createForm(StaffRoleType::class, $entity, array(
            'action' => $this->generateUrl('staffrole_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        
        $form->add('submit', SubmitType::class, 
            array( 'label' => 'Update',
             'attr' => ['class' => 'btn-primary'])
        );

        return $form;
    }
    /**
     * Edits an existing StaffRole entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LevCRMBundle:StaffRole')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StaffRole entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('staffrole'));
        }

        return $this->render('LevCRMBundle:StaffRole:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a StaffRole entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LevCRMBundle:StaffRole')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find StaffRole entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('staffrole'));
    }

    /**
     * Creates a form to delete a StaffRole entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('staffrole_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, 
                array( 'label' => 'Delete',
                    'attr' => ['class' => 'btn-danger'])
                )
            ->getForm()
        ;
    }
}
