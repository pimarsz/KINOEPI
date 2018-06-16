<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-13
 * Time: 20:38
 */

/**
 * Transaction type.
 */
namespace Form;

use Repository\TransactionRepository;
use Repository\PaymentMethodRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Validator\Constraints as CustomAssert;

/**
 * Order TransactionType.
 */
class TransactionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'payment_method',
            ChoiceType::class,
            [
                'label' => 'label.payment_method',
                'required' => true,
                'placeholder' =>'label.choose_payment_method',
                'choices' => $this->preparePaymentMethodForChoices($options['paymentmethod_repository']),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'transaction_type';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'payments-default',
                'paymentmethod_repository' => null,
            ]
        );
    }

    protected function preparePaymentMethodForChoices($PaymentMethodRepository)
    {

        $statuses = $PaymentMethodRepository->findAll();
        $choices = [];

        foreach ($statuses as $payment) {
            $choices[$payment['method_name']] = $payment['payment_method_id'];
        }

        return $choices;
    }

}