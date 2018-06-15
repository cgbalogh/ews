<?php
namespace CGB\Ews\Domain\Model;

/***
 *
 * This file is part of the "ews" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2017
 *
 ***/

/**
 * Credentials
 */
class Credential extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Username
     *
     * @var string
     * @validate NotEmpty
     */
    protected $username = '';

    /**
     * Exchange User
     *
     * @var string
     */
    protected $exchangeUsername = '';

    /**
     * Exchange Password
     *
     * @var string
     */
    protected $exchangePassword = '';

    /**
     * Returns the username
     *
     * @return string $username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the username
     *
     * @param string $username
     * @return void
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Returns the exchangeUsername
     *
     * @return string $exchangeUsername
     */
    public function getExchangeUsername()
    {
        return $this->exchangeUsername;
    }

    /**
     * Sets the exchangeUsername
     *
     * @param string $exchangeUsername
     * @return void
     */
    public function setExchangeUsername($exchangeUsername)
    {
        $this->exchangeUsername = $exchangeUsername;
    }

    /**
     * Returns the exchangePassword
     *
     * @return string $exchangePassword
     */
    public function getExchangePassword()
    {
        return \CGB\Ews\Service\CryptoService::decrypt($this->exchangePassword, $this->getUid() . $this->username);
    }

    /**
     * Sets the exchangePassword
     *
     * @param string $exchangePassword
     * @return void
     */
    public function setExchangePassword($exchangePassword)
    {
        $this->exchangePassword = \CGB\Ews\Service\CryptoService::encrypt($exchangePassword, $this->getUid() . $this->username);
    }
}
