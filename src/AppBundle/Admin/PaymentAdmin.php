<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class PaymentAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('user')
            ->add('itinerary')
            ->add('number')
            ->add('description')
            ->add('reservationDate')
            ->add('clientEmail')
            ->add('clientId')
            ->add('totalAmount')
            ->add('currencyCode')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('user')
            ->add('itinerary')
            ->add('number')
            ->add('description')
            ->add('totalAmount')
            ->add('currencyCode')
            ->add('reservationDate')
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
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
            ->add('id')
            ->add('user')
            ->add('itinerary')
            ->add('number')
            ->add('description')
            ->add('clientEmail')
            ->add('clientId')
            ->add('totalAmount')
            ->add('currencyCode')
            ->add('reservationDate')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('user')
            ->add('itinerary')
            ->add('number')
            ->add('description')
            ->add('clientEmail')
            ->add('clientId')
            ->add('totalAmount')
            ->add('currencyCode')
            ->add('reservationDate')
        ;
    }
}
