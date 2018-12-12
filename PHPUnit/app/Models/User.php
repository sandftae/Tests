<?php

namespace App\Models;

/**
 * Class User
 * @package App\Models
 */
class User
{
    /**
     * @var $first_name
     */
    public $first_name;

    /**
     * @var $last_name
     */
    public $last_name;

    /**
     * @var $_email
     */
    public $_email;

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->first_name = trim($firstName);
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->last_name = trim($lastName);
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->last_name;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->_email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->_email;
    }

    /**
     * @return array
     */
    public function getEmailVariables(): array
    {
        return
            [
                'full_name' => $this->getFullName(),
                'email' => $this->getEmail()
            ];
    }
}
