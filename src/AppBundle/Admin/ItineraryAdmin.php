<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ItineraryAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user')
            ->add('contents')
            ->add('parent')
            ->add('creationDate')
            ->add('lastModificationDate')
            ->add('title')
            ->add('slug')
            ->add('description')
            ->add('image')
            ->add('paid')
            ->add('price')
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
            ->add('parent')
            ->add('contents')
            ->add('creationDate')
            ->add('paid')
            ->add('price')
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
            ->add('user')
            ->add('contents')
            ->add('parent')
            ->add('creationDate')
            ->add('lastModificationDate')
            ->add('title')
            ->add('slug')
            ->add('description')
            ->add('image')
            ->add('paid')
            ->add('price')
        ;
    }
}
