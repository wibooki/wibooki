<?php

namespace AppBundle\Form;

use AppBundle\Entity\Content;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class ContentType extends AbstractType
{
    private $user;
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $homeFolder = $options['home_folder'];
        $this->user = $options['user'];
        // Don't allow for root folder access
        if (empty($homeFolder)) {
            return false;
        }

        $builder
            ->add('title')
            ->add('description')
            ->add('itineraries', EntityType::class, [
                'required' => true,
                'multiple' => true,
                'class' => 'AppBundle:Itinerary',
                'choice_label' => 'title',
                'label' => "Itinerarios",
                'attr' => [
                    'placeholder' => "Itinerarios",
                    'class' => 'places form-control',
                ],
                'empty_data'  => null,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.user = :user')
                        ->setParameter('user',$this->user)
                        ;
                }
            ])

            ->add('demo', CheckboxType::class, [
                'label' => 'form.demo.label',
                'required' => false,
            ])
            ->add('contentText', CKEditorType::class, [
                'config' => [
                    'filebrowserBrowseRoute' => 'elfinder',
                    'filebrowserBrowseRouteParameters' => [
                        'instance' => 'ckeditor',
                        'homeFolder' => $homeFolder
                    ]
                ]
            ])
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if ($data && count($data->getItineraries()) > 0) {
                ;
            } else {
                $form
                    ->add('itineraries', CollectionType::class, array(
                        'entry_type' => ItineraryType::class,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                    ))
                ;
            }
        });
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Content::class,
            'home_folder' => 'ErrorFolder',
            'user' => null,
            'translation_domain' => 'content'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_content';
    }
}
