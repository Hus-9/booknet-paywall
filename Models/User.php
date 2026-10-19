<?php

require_once __DIR__ . '/Database.php';

// Encapsulates user persistence and lookup.
class User
{
    /**
     * Find a user row by email.
     *
     * @param string $email
     * @return array|null
     */
    public static function findByEmail($email)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return $user;
        }
        return null;
    }

    /**
     * Find a user row by id.
     *
     * @param int $id
     * @return array|null
     */
    public static function findById($id)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return $user;
        }
        return null;
    }

    /**
     * Create a new user with hashed password.
     * New users start on the "Free" role by default.
     *
     * @param string $email
     * @param string $passwordHash
     * @return bool
     */
    public static function create($email, $passwordHash)
    {
        $db = Database::getConnection();
        // Use the role column instead of is_premium.
        $stmt = $db->prepare(
            'INSERT INTO users (email, password_hash, role) 
             VALUES (:email, :password_hash, :role)'
        );
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->bindValue(':role', 'Free', PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Determine if a user has an active subscription based on role.
     * Reader & BookNet Pro are treated as "subscribed".
     *
     * @param int $userId
     * @return bool
     */
    public static function isSubscribed($userId)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT role FROM users WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || empty($row['role'])) {
            return false;
        }

        $role = $row['role'];
        $subscribedRoles = array('Reader', 'BookNet Pro');

        return in_array($role, $subscribedRoles, true);
    }

    /**
     * Upgrade a user to a premium-style role.
     * Here we default to "Reader" as the upgrade target.
     *
     * @param int $userId
     * @return bool
     */
    public static function upgradeToPremium($userId)
    {
        return self::updateRole($userId, 'Reader');
    }

    /**
     * Generic helper: update a user's role.
     * Useful for paywall logic when selecting between
     * Free, Graphical, Reader, BookNet Pro.
     *
     * @param int $userId
     * @param string $role
     * @return bool
     */
    public static function updateRole($userId, $role)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('UPDATE users SET role = :role WHERE id = :id');
        $stmt->bindValue(':role', $role, PDO::PARAM_STR);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}