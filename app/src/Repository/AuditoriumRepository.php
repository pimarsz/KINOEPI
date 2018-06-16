<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-05
 * Time: 15:37
 */
/**
* Auditorium repository.
 */
namespace Repository;
use Doctrine\DBAL\Connection;
use Form\ScreeningType;

/**
 * Class AuditoriumRepository.
 */

class AuditoriumRepository
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
        $queryBuilder->select('auditorium_id', 'name', 'seats_no')
            ->from('auditorium');


        return $queryBuilder->execute()->fetchAll();
    }
    /**
     * Finds auditorium by its id.
     *
     * @param int $id
     *
     * @return array Result
     */
    public function findOneById($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('auditorium_id', 'name', 'seats_no')
            ->from('auditorium')
            ->where('auditorium_id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);

        $result = $queryBuilder->execute()->fetch();

        return !$result ? [] : $result;
    }
    /**
     * Finds linked auditoriums Ids.
     *
     * @param int $auditorium_id Auditorium id
     *
     * @return array Result
     */
    protected function findLinkedAuditoriumsIds($auditorium_id)
    {
        $queryBuilder = $this->db->createQueryBuilder()
            ->select('s.auditorium_auditorium_id')
            ->from('screening', 's')
            ->where('s.auditorium_auditorium_id = :auditorium_auditorium_id')
            ->setParameter(':auditorium_auditorium_id', $auditorium_id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetchAll();

        return isset($result) ? array_column($result, 'auditorium_id') : [];
    }

    /**
     * Remove linked auditorium.
     *
     * @param int $auditorium_id Auditorium ID
     *
     * @return boolean Result
     */
    protected function removeLinkedTags($auditorium_id)
    {
        return $this->db->delete('screening', ['auditorium_auditorium_id' => $auditorium_id]);
    }

    /**
     * Add linked auditoriums.
     *
     * @param int $screening_id Screening ID
     * @param array $auditorium_ids Auditorium Ids
     */
    protected function addLinkedAuditoriums($screening_id, $auditorium_ids)
    {
        if (!is_array($auditorium_ids)) {
            $auditorium_ids = [$auditorium_ids];
        }

        foreach ($auditorium_ids as $auditorium_id) {
            $this->db->insert(
                'screening',
                [
                    'screening_id' => $screening_id,
                    'auditorium_id' => $auditorium_id,
                ]
            );
        }
    }
    public function save($auditorium)
    {
        if (isset($auditorium['auditorium_id']) && ctype_digit((string) $auditorium['auditorium_id'])) {
            // update record
            $id = $auditorium['auditorium_id'];
            unset($auditorium['auditorium_id']);

            return $this->db->update('auditorium', $auditorium, ['auditorium_id' => $id]);
        } else {
            // add new record
            return $this->db->insert('auditorium', $auditorium);
        }
    }
    /**
     * Query all records.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder Result
     */
    protected function queryAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select('a.auditorium_id', 'a.name')
            ->from('auditorium', 'a');
    }


}

