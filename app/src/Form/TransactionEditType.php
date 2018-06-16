<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-16
 * Time: 11:51
 */

/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-13
 * Time: 20:38
 */

/**
 * TransactionEdit type.
 */
namespace Form;

use Repository\TransactionRepository;
use Repository\PaymentStatusRepository;
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
class TransactionEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'payment_status_payment_status_id',
            ChoiceType::class,
            [
                'label' => 'label.payment_status',
                'required' => true,
                'placeholder' =>'label.choose_payment_status',
                'choices' => $this->preparePaymentStatusForChoices($options['paymentstatus_repository']),
            ]
        );
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'transactionedit_type';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'payments-default',
                'paymentstatus_repository' => null,
            ]
        );
    }

    protected function preparePaymentStatusForChoices($PaymentStatusRepository)
    {

        $statuses = $PaymentStatusRepository->findAll();
        $choices = [];

        foreach ($statuses as $payment) {
            $choices[$payment['status_name']] = $payment['payment_status_id'];
        }

        return $choices;
    }

}