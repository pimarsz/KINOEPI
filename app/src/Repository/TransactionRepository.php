<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-13
 * Time: 20:37
 */


/**
 * Transactions repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;


/**
 * Class TransactionRepository.
 */
class TransactionRepository
{
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * OrderRepository constructor.
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
        $queryBuilder->select('transaction_id', 'p.method_name', 'users_user_id', 'date','ticket_ticket_id',
            's.status_name')
            ->from('transactions', 't')
            ->innerJoin('t', 'payment_method', 'p', 't.payment_method_payment_method_id = p.payment_method_id')
            ->innerJoin('t', 'payment_status', 's', 't.payment_status_payment_status_id = s.payment_status_id ')
            -> orderBy('t.date');

        return $queryBuilder->execute()->fetchAll();
    }


    /**
     * @param $id
     * @return array
     */
    public function findOneById($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('t.transaction_id', 't.payment_method_payment_method_id',
            't.payment_status_payment_status_id', 't.users_user_id', 't.date', 't.ticket_ticket_id')
            ->from('transactions', 't')
            ->innerJoin('t', 'payment_method', 'p', 's.payment_method_payment_method_id = p.payment_method_id')
            ->innerJoin('t', 'payment_status', 's', 't.payment_status_payment_status_id = s.payment_status_id ')
            ->where('t.transaction_id=:id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetchAll();

        return $result;
    }

    public function findOneTransactionById($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('t.transaction_id', 't.payment_method_payment_method_id',
            't.payment_status_payment_status_id', 't.users_user_id', 't.date', 't.ticket_ticket_id')
            ->from('transactions', 't')
            ->where('t.transaction_id=:id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetch();

        return $result;
    }

    public function findAllTransactions()
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('t.transaction_id', 'p.method_name',
            't.payment_status_payment_status_id', 't.users_user_id', 't.date', 't.ticket_ticket_id', 'p.method_name')
            ->from('transactions', 't')
            ->innerJoin('t', 'payment_method', 'p', 't.payment_method_payment_method_id = p.payment_method_id')
            ->innerJoin('t', 'payment_status', 's', 't.payment_status_payment_status_id = s.payment_status_id ')
            -> orderBy('t.date');

        return $queryBuilder->execute()->fetchAll();
    }


    /**
     * @param $transaction
     * @param $userId
     * @return \Doctrine\DBAL\Driver\Statement|int
     */
    public function save($transaction, $userId)
    {
        if (isset($transaction['id']) && ctype_digit((string) $transaction['id'])) {

            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->insert('transactions')
                ->values(
                    array(
                        'ticket_ticket_id' => ':ticket_id',
                        'payment_method_payment_method_id' => ':paymethod',
                        'users_user_id' => ':user_id'
                    )
                )
                ->setParameter(':ticket_id', $transaction['id'], \PDO::PARAM_INT)
                ->setParameter(':paymethod',  $transaction['payment_method'], \PDO::PARAM_INT)
                ->setParameter(':user_id',  $userId, \PDO::PARAM_INT);
            $result = $queryBuilder->execute();

            return $result;
        } else {
            return 0;
        }
    }

    /**
     * @param $transaction
     * @return \Doctrine\DBAL\Driver\Statement|int
     */
    public function saveEdited($transaction)
    {
        if (isset($transaction['id']) && ctype_digit((string) $transaction['id'])) {

            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->update('transactions')
                ->set
                (
                        'payment_status_payment_status_id', ':paystatus'
                 )
                -> where('transaction_id=:transaction_id')
                ->setParameter(':paystatus',  $transaction['payment_status_payment_status_id'], \PDO::PARAM_INT)
                ->setParameter(':transaction_id', $transaction['id'], \PDO::PARAM_INT);
            $result = $queryBuilder->execute();

            return $result;
        } else {
            return 0;
        }
    }



}