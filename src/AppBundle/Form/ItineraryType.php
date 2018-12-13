<?php

namespace AppBundle\Form;

use AppBundle\Entity\Itinerary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItineraryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('paid', ChoiceType::class, [
                'label' => false,
                'required' => true,
                'expanded' => true,
                'choices'  => [
                    'form.free.value' => false,
                    'form.paid.value' => true,
                ],
            ])
            ->add('price',null,[
                'label' => 'form.price.label',
                'attr' => [
                    'class' => 'itrPrice',
                    'placeholder' => 'form.price.placeholder'
                ]
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
         $resolver->setDefaults([
             'data_class' => Itinerary::class,
             'translation_domain' => 'itinerary'
         ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_itinerary';
    }


}
