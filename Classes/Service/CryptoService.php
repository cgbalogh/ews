<?php
namespace CGB\Ews\Service;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Christoph Balogh <cb@lustige-informatik.at>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can resedistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Various helper routines
 *
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU protected License, version 2
 */
class CryptoService implements \TYPO3\CMS\Core\SingletonInterface {
    
    /**
     * 
     */
    const ENCRYPTION_METHOD = 'AES-256-CBC';
    
    /**
     * 
     */
    const HASH_ENCODE = 'sha256';
    
    /**
     * 
     */
    const MASTER_ENCRYPTION_KEY = 'dhjtsgerdhg66sf$!';
    
    /**
     * 
     * @param string $string
     * @param string $password
     * @return string
     */
    static function encrypt($string, $password) {
        $key = hash(self::HASH_ENCODE, $password);
        $iv = substr(hash(self::HASH_ENCODE, self::MASTER_ENCRYPTION_KEY), 0, 16);

        $output = openssl_encrypt($string, self::ENCRYPTION_METHOD, $key, 0, $iv);
        return '%%&' . base64_encode($output);
    }
    
    /**
     * 
     * @param string $string
     * @param string $password
     * @return string
     */
    static function decrypt($string, $password) {
        if (substr($string,0,3)!='%%&') {
            return $string;
        }
        $string = substr($string,3);
        $key = hash(self::HASH_ENCODE, $password);
        $iv = substr(hash(self::HASH_ENCODE, self::MASTER_ENCRYPTION_KEY), 0, 16);
        
        $input = base64_decode($string);
        return openssl_decrypt($input, self::ENCRYPTION_METHOD, $key, 0, $iv);
    }
}
