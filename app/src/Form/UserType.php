<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-15
 * Time: 18:10
 */

/**
 * User type.
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Validator\Constraints as CustomAssert;

/**
 * Class UserType.
 */
class UserType extends AbstractType
{


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'login',
            TextType::class,
            [
                'label' => 'Login',
                'required' => true,
                'attr' => [
                    'max_length' => 45,
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['user-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['user-default'],
                            'min' => 3,
                            'max' => 128,
                        ]
                    ),
                    new CustomAssert\UniqueUser(
                        ['groups' => ['user-default'],
                            'repository' => isset($options['user_repository']) ? $options['user_repository'] : null,
                            'elementId' => isset($options['data']['id']) ? $options['data']['id'] : null,
                        ]
                    ),
                ],
            ]

        )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' => array('label' => 'label.password'),
                    'second_options' => array('label' => 'label.re_password'),
                    'required' => true,
                    'attr' => [
                        'min_length' => 8,
                        'max_length' => 32,
                    ],
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Length(
                            [
                                'min' => 8,
                                'max' => 32,
                            ]
                        ),
                    ],
                ]
            );
        $builder->add(
            'role_id',
            HiddenType::class,
            array(
                'data' => '2',
            )
        );

        /**
         * {@inheritdoc}
         */

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'user-default',
                'user_repository' => null,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'user_type';
    }
}