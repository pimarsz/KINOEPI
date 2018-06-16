<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-15
 * Time: 18:11
 */
/**
 * Unique User constraint.
 */
namespace Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueUser.
 */
class UniqueUser extends Constraint
{
    /**
     * Message.
     *
     * @var string $message
     */
    public $message = 'message.badlogin';

    /**
     * Element id.
     *
     * @var int|string|null $elementId
     */
    public $elementId = null;

    /**
     * Tag repository.
     *
     * @var null|\Repository\UserRepository $repository
     */
    public $repository = null;

}