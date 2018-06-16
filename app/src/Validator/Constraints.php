<?php
/**
 * Proper Order constraint.
 */
namespace Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class ProperOrder.
 */
class ProperOrder extends Constraint
{
    /**
     * Message.
     *
     * @var string $message
     */
    public $message = 'There is not enough space for this screening';

}