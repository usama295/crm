<?php

namespace App\Lev\CRMBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Lev\CRMBundle\Entity\Staff;
use App\Lev\CRMBundle\Form\StaffType;
use App\Lev\CRMBundle\Form\Type\StaffEditFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Swagger\Annotations as SWG;
/**
 * Staff controller.
 *
 */
class StaffController extends Controller
{

    /**
     * @SWG\Tag(name="Staff")
     * @SWG\Response(
     *    response=200,
     *    description="List all staff")
     * Lists all Staff entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LevCRMBundle:Staff')->findAll();

        return $this->render('LevCRMBundle:Staff:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Staff entity.
     * @SWG\Tag(name="Staff")
     * @SWG\Response(
     *    response=200,
     *    description="create new staff")
     */
    public function createAction(Request $request)
    {
        $entity = new Staff();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('staff'));
        }

        return $this->render('LevCRMBundle:Staff:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Staff entity.
     * @param Staff $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Staff $entity)
    {
        $form = $this->createForm(StaffType::class, $entity, array(
            'action' => $this->generateUrl('staff_create'),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, 
            array( 'label' => 'Create',
             'attr' => ['class' => 'btn-primary'])
        );
        
        return $form;
    }

    /**
     * Displays a form to create a new Staff entity.
     *
     */
    public function newAction()
    {
        $entity = new Staff();
        $form   = $this->createCreateForm($entity);

        return $this->render('LevCRMBundle:Staff:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Staff entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LevCRMBundle:Staff')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Staff entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LevCRMBundle:Staff:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Staff entity.
    *
    * @param Staff $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Staff $entity)
    {
        $form = $this->createForm(StaffEditFormType::class, $entity, array(
            'action' => $this->generateUrl('staff_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', SubmitType::class, 
            array( 'label' => 'Update',
             'attr' => ['class' => 'btn-primary'])
        );

        return $form;
    }
    /**
     * Edits an existing Staff entity.
     * @SWG\Tag(name="Staff")
     * @SWG\Response(
     *    response=200,
     *    description="update staff")
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LevCRMBundle:Staff')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Staff entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('staff'));
        }

        return $this->render('LevCRMBundle:Staff:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Staff entity.
     * @SWG\Tag(name="Staff")
     * @SWG\Response(
     *    response=200,
     *    description="delete staff")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LevCRMBundle:Staff')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Staff entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('staff'));
    }

    /**
     * Creates a form to delete a Staff entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('staff_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, 
                array( 'label' => 'Delete',
                'attr' => ['class' => 'btn-danger'])
             )
            ->getForm()
        ;
    }
}
