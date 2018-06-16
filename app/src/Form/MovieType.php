<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-02
 * Time: 10:28
 */

/**
 * Movie type.
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Movie TagType.
 */
class MovieType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'movie_title',
            TextType::class,
            [
                'label' => 'label.name',
                'required' => true,
                'attr' => [
                    'max_length' => 128,
                ],
            ]
        );
        $builder->add(
            'movie_description',
            TextareaType::class,
            [
                'label' => 'label.description',
                'required' => true,
                'attr' => [
                    'max_length' => 500,
                    'class' => 'form-control',
                ],
            ]
        );
        $builder->add(
            'movie_duration_min',
            NumberType::class,
            [
                'label' => 'label.duration',
                'required' => true,
                'attr' => [
                    'max_length' => 45,
                    'class' => 'form-control',
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'movie_type';
    }
}