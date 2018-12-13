<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ContentAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('slug')
            ->add('user')
            ->add('itineraries')
            ->add('image')
            ->add('creationDate')
            ->add('lastModificationDate')
            ->add('demo')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('image')
            ->add('title')
            ->add('slug')
            ->add('user')
            ->add('itineraries')
            ->add('creationDate')
            ->add('demo')
            ->add('_action', null, array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                ),
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title')
            ->add('slug')
            ->add('user')
            ->add('itineraries')
            ->add('image')
            ->add('creationDate')
            ->add('lastModificationDate')
            ->add('demo')
            ->add('description')
            ->add('contentText')
        ;
    }
}
