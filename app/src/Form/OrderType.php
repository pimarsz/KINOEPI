<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-04
 * Time: 11:46
 */
/**
 * Order type.
 */
namespace Form;

use Repository\OrderRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Validator\Constraints as CustomAssert;

/**
 * Order ScreeningType.
 */
class OrderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'seats_amount',
            NumberType::class,
            [
                'label' => 'label.seats_no',
                'required' => true,
                'attr' => [
                    'max_length' => 500,
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['seats-default']]
                    ),
                    new Assert\LessThanOrEqual(
                        [
                            'groups' => ['seats-default'],
                            'value' => $options['data']['order_repository']->CountSeats($options['data']['id']),
                            'message' => 'message.notenoughseats',
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'order_type';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'seats-default',
//                'order_repository' => null,
            ]
        );
    }

}