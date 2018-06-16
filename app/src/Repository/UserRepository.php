<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-14
 * Time: 17:21
 */
/**
 * User repository
 */

namespace Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Class UserRepository.
 */
class UserRepository
{
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * TagRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Loads user by login.
     *
     * @param string $login User login
     * @throws UsernameNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array Result
     */
    public function loadUserByLogin($login)
    {
        try {
            $user = $this->getUserByLogin($login);

            if (!$user || !count($user)) {
                throw new UsernameNotFoundException(
                    sprintf('Username "%s" does not exist.', $login)
                );
            }

            $roles = $this->getUserRoles($user['id']);

            if (!$roles || !count($roles)) {
                throw new UsernameNotFoundException(
                    sprintf('Username "%s" does not exist.', $login)
                );
            }

            return [
                'login' => $user['login'],
                'password' => $user['password'],
                'roles' => $roles,
            ];
        } catch (DBALException $exception) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $login)
            );
        } catch (UsernameNotFoundException $exception) {
            throw $exception;
        }
    }

    /**
     * Gets user data by login.
     *
     * @param string $login User login
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array Result
     */
    public function getUserByLogin($login)
    {
        try {
            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->select('u.id', 'u.login', 'u.password')
                ->from('si_users', 'u')
                ->where('u.login = :login')
                ->setParameter(':login', $login, \PDO::PARAM_STR);

            return $queryBuilder->execute()->fetch();
        } catch (DBALException $exception) {
            return [];
        }
    }

    /**
     * @param $login
     * @return array|mixed
     */
    public function getIdByLogin($login)
    {
        try {
            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->select('u.id')
                ->from('si_users', 'u')
                ->where('u.login = :login')
                ->setParameter(':login', $login, \PDO::PARAM_STR);
            return $queryBuilder->execute()->fetch();
        } catch (DBALException $exception) {
            return [];
        }
    }
    /**
     * Gets user roles by User ID.
     *
     * @param integer $userId User ID
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array Result
     */
    public function getUserRoles($userId)
    {
        $roles = [];

        try {
            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->select('r.name')
                ->from('si_users', 'u')
                ->innerJoin('u', 'si_roles', 'r', 'u.role_id = r.id')
                ->where('u.id = :id')
                ->setParameter(':id', $userId, \PDO::PARAM_INT);
            $result = $queryBuilder->execute()->fetchAll();

            if ($result) {
                $roles = array_column($result, 'name');
            }

            return $roles;
        } catch (DBALException $exception) {
            return $roles;
        }
    }

    /**
     * Save record.
     *
     * @param array $user User
     *
     * @return boolean Result
     */
    public function save($user)
    {
        if (isset($user['id']) && ctype_digit((string)$user['id'])) {
            // update record
            $id = $user['id'];
            unset($user['id']);

            return $this->db->update('si_users', $user, ['id' => $id]);
        } else {
            // add new record
            return $this->db->insert('si_users', $user);
        }
    }

    /**
     * Save record.
     *
     * @param array $user User
     *
     * @return boolean Result
     */
    public function saveEdited($user)
    {
        if (isset($user['id']) && ctype_digit((string)$user['id'])) {
            // update record
            $id = $user['id'];
            unset($user['id']);

            return $this->db->update('si_users', $user, ['id' => $id]);
        } else {
            // add new record
            return $this->db->insert('si_users', $user);
        }
    }
//    public function saveEdited($user)
//    {
////        if (isset($user['login'])  && isset($user['password']))  {
////            // update record
////            $user['role_id'] = 2;
////
////            return $this->db->insert('si_users', $user);
////        }
//        if (isset($user['id']) && ctype_digit((string) $user['id'])) {
//
//            $queryBuilder = $this->db->createQueryBuilder();
//            $queryBuilder->update('si_users')
//                ->set
//                (
//                    'role_id', ':role'
//                )
//                -> where('id=:id')
//                ->setParameter(':role',  $user['role_id'], \PDO::PARAM_INT)
//                ->setParameter(':id', $user['id'], \PDO::PARAM_INT);
//            $result = $queryBuilder->execute();
//
//            return $result;
//        } else {
//            return 0;
//        }
//    }

//    /**
//     * Find for uniqueness.
//     *
//     * @param string          $user_login Element user_login
//     * @param int|string|null $id   Element id
//     *
//     * @return array Result
//     */
//    public function findForUniqueness($user_login, $id = null)
//    {
//        $queryBuilder = $this->queryAll();
//        $queryBuilder->where('u.login = :login')
//            ->setParameter(':login', $user_login, \PDO::PARAM_STR);
//        if ($id) {
//            $queryBuilder->andWhere('u.user_id <> :id')
//                ->setParameter(':id', $id, \PDO::PARAM_INT);
//        }
//
//        return $queryBuilder->execute()->fetchAll();
//    }

    /**
     * Fetch all records.
     *
     * @return array Result
     */
    public function findAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('u.id', 'u.login', 'u.password', 'u.role_id')
            ->from('si_users', 'u')
            ->innerJoin('u', 'si_roles', 's', 'u.role_id= s.id');

        return $queryBuilder->execute()->fetchAll();
    }

//    public function findUserById($id)
//    {
//        $queryBuilder = $this->db->createQueryBuilder();
//        $queryBuilder->select('u.id', 'u.login', 'u.password', 'u.role_id')
//            ->from('si_users', 'u')
//            ->where('u.id=:id')
//            ->setParameter(':id', $id, \PDO::PARAM_INT);
//        $result = $queryBuilder->execute()->fetch();
//
//        return $result;
//    }

    /**
     * Find one record.
     *
     * @param string $id Element id
     *
     * @return array|mixed Result
     */
    public function findOneById($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('id', 'login', 'password', 'role_id')
            ->from('si_users')
            ->where('id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetch();

        return !$result ? [] : $result;
    }

    public function delete($user)
    {
        return $this->db->delete('user', ['id' => $user['id']]);
    }
}