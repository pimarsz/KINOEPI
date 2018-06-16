<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-15
 * Time: 18:12
 */

/**
 * Unique User validator.
 */
namespace Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UniqueUserValidator.
 */
class UniqueUserValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint->repository) {
            return;
        }

        $result = $constraint->repository->findForUniqueness(
            $value,
            $constraint->elementId
        );

        if ($result && count($result)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ user_login }}', $value)
                ->addViolation();
        }
    }
}