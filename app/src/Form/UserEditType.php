<?php
/**
 * User type.
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;




/**
 * Class UserType.
 */
class UserEditType extends AbstractType
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
                'label' => 'label.login',
                'required' => true,
                'attr' => [
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
//        $builder->add(
//            'user_password',
//            PasswordType::class,
//            [
//                'label' => 'label.password',
//                'required' => true,
//                'attr' => [
//                    'max_length' => 32,
//
//                ],
//                'constraints' => [
//                    new Assert\NotBlank(),
//                    new Assert\Length(
//                        [
//                            'min' => 8,
//                            'max' => 32,
//                        ]
//                    ),
//                ],
//            ]
//        );

        $builder->add(
            'role_id',
            ChoiceType::class,
            [
                'label' => 'label.role',
                'required' => true,
                'choices'  => array(
                    'label.User' => '2',
                    'label.Admin' => '1',
                ),
            ]);


    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'user-default',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'useredit_type';
    }
}