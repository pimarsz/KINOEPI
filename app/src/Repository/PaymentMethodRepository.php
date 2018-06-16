<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-13
 * Time: 21:01
 */

/**
 * Payment Method repository.
 */
namespace Repository;
use Doctrine\DBAL\Connection;
use Form\TransactionType;

/**
 * Class PaymentMethodRepository.
 */

class PaymentMethodRepository
{

    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * AuditoriumRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Fetch all records.
     *
     * @return array Result
     */
    public function findAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('payment_method_id', 'method_name')
            ->from('payment_method');


        return $queryBuilder->execute()->fetchAll();
    }
}