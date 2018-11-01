<?php

namespace App\Form;

use App\Entity\Job;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('service_id')
            ->add('title')
            ->add('city_id')
            ->add('zip_code')
            ->add('description')
            ->add(
                'end_date',
                DateTimeType::class,
                [
                    'widget'        => 'single_text',
                    'format'        => 'yyyy-MM-dd H:i:s',
                    'property_path' => 'end_date',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
