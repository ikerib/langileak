<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class PasaiaLdapService
{
    private string $ip;
    private string $ldap_username;
    private string $basedn;
    private string $passwd;
    private string $ldapAdminTaldea;
    private string $ldapKudeatuTaldea;
    private string $ldapUserTaldea;
    private EntityManagerInterface $em;


    public function __construct(string $ip, string $ldap_username, string $basedn, string $passwd, string $ldapAdminTaldea, string $ldapKudeatuTaldea, string $ldapUserTaldea, EntityManagerInterface $em)
    {
        $this->ip = $ip;
        $this->ldap_username = $ldap_username;
        $this->basedn = $basedn;
        $this->passwd = $passwd;
        $this->ldapAdminTaldea = $ldapAdminTaldea;
        $this->ldapKudeatuTaldea = $ldapKudeatuTaldea;
        $this->ldapUserTaldea = $ldapUserTaldea;
        $this->em = $em;
    }

    public function checkCredentials($username, $password): bool
    {
        $ip = $this->ip;
        $searchdn = "CN=$username,CN=Users,DC=pasaia,DC=net";

        /**
         * LDAP KONTSULTA EGIN erabiltzailearen bila
         */
        $srv = "ldap://$ip:389";
        $ldap = Ldap::create('ext_ldap', ['connection_string' => $srv]);
        try {
            $ldap->bind($searchdn, $password);
        } catch (ConnectionException $e) {
            throw new CustomUserMessageAuthenticationException('Pasahitza ez da zuzena.');
        }

        return true;
    }

    public function getLdapInfoByUsername($username)
    {
        $ip = $this->ip;
        $basedn = $this->basedn;
        $passwd = $this->passwd;

        /**
         * LDAP KONTSULTA EGIN erabiltzailearen bila
         */
        $srv = "ldap://$ip:389";
        $ldap = Ldap::create('ext_ldap', ['connection_string' => $srv]);
        $ldap->bind('CN=izfeprint,CN=Users,DC=pasaia,DC=net', $passwd);
        $query = $ldap->query($basedn, "(sAMAccountName=$username)", array());

        return $query->execute();
    }

    public function updateDbUserDataFromLdapByUsername($username): User
    {
        $dbUser = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
        $ldapQuery = $this->getLdapInfoByUsername($username);
        $ldapData = $ldapQuery[0];
        $dbUser = $this->syncUserInfoFromLdap($dbUser, $ldapData);

        return $dbUser;
    }

    public function createDbUserFromLdapData($username): User
    {
        $ldapQuery = $this->getLdapInfoByUsername($username);
        /** @var Entry $ldapData */
        $ldapData = $ldapQuery[0];
        $user = new User();
        $user->setUsername($username);
        $user = $this->syncUserInfoFromLdap($user, $ldapData);

        return $user;
    }

    /**
     * @param User $user
     * @param Entry $ldapData
     *
     * @return User|null
     */
    private function syncUserInfoFromLdap(User $user, $ldapData): ?User
    {

        $ldap = $ldapData->getAttributes();

        if (array_key_exists('employeeID', $ldap)) {
            $user->setNan((string)$ldap['employeeID'][0]);
        }

        if (array_key_exists('preferredLanguage', $ldap)) {
            $user->setHizkuntza((string)$ldap['preferredLanguage'][0]);
        }

        if (array_key_exists('mail', $ldap)) {
            $user->setEmail((string)$ldap['mail'][0]);
        }

        if (array_key_exists('givenName', $ldap)) {
            $user->setFirstname((string)$ldap['givenName'][0]);
        }

        if (array_key_exists('sn', $ldap)) {
            $user->setSurname((string)$ldap['sn'][0]);
        }

        if ((array_key_exists('givenName', $ldap)) && (array_key_exists('sn', $ldap))) {
            $user->setDisplayname((string)$ldap['givenName'][0] . ' ' . (string)$ldap['sn'][0]);
        } elseif (!array_key_exists('givenName', $ldap)) {
            $user->setDisplayname((string)$ldap['sn'][0]);
        } elseif (!array_key_exists('sn', $ldap)) {
            $user->setDisplayname((string)$ldap['givenName'][0]);
        } else {
            $user->setDisplayname($user->getUsername());
        }

        if (array_key_exists('description', $ldap)) {
            $user->setLanpostua((string)$ldap['description'][0]);
        }

        if (array_key_exists('department', $ldap)) {
            $user->setDeparment((string)$ldap['department'][0]);
        }

        $sailburuArr = $this->checkSailburuada($user->getUsername());
        $rol = ['ROLE_PASAIA'];
        if ($sailburuArr['sailburuada']) {
            $rol[] = 'ROLE_SAILBURUA';
        }
        $user->setSailburuada($sailburuArr['sailburuada']);
        $ldapTaldeak = $this->getLdapUserMembershipGroupsRecursivelyByUsername($user->getUsername());
        $user->setLdapTaldeak($ldapTaldeak);

        // begiratu talde bat baino gehiago dituen komaz bereizita
        $lat = []; //array ldap admin taldeak
        $lkt = []; //array ldap kudeatu taldeak
        $lut = []; //array ldap user taldeak
        $hasRole = false; // if this value is false the user exist in LDAP but it is not assigned to any valid LDAP GROUP
        if (str_contains($this->ldapAdminTaldea, ',')){
            $lat = explode(',', $this->ldapAdminTaldea);
            $lat = array_map('trim',$lat);
        } else {
            $lat[] = $this->ldapAdminTaldea;
        }
        if (str_contains($this->ldapKudeatuTaldea, ',')){
            $lkt = explode(',', $this->ldapAdminTaldea);
            $lkt = array_map('trim',$lkt);
        } else {
            $lkt[] = $this->ldapAdminTaldea;
        }
        if (str_contains($this->ldapUserTaldea, ',')){
            $lut = explode(',', $this->ldapAdminTaldea);
            $lut = array_map('trim',$lut);
        } else {
            $lut[] = $this->ldapAdminTaldea;
        }

        foreach ($lat as $at) {
            if ($this->in_array_r($at, $ldapTaldeak, false)) {
                $rol[] = 'ROLE_ADMIN';
                $hasRole = true;
            }
        }

        foreach ($lkt as $at) {
            if ($this->in_array_r($at, $ldapTaldeak, false)) {
                $rol[] = 'ROLE_KUDEATU';
                $hasRole = true;
            }
        }

        foreach ($lut as $at) {
            if ($this->in_array_r($at, $ldapTaldeak, false)) {
                $rol[] = 'ROLE_USER';
                $hasRole = true;
            }
        }

        if ( $hasRole !== true ) {
            throw new CustomUserMessageAuthenticationException("Erabiltzailea ez da beharrezko LDAP taldekoa.");
        }
        $user->setRoles($rol);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function in_array_r($needle, $haystack, $strict = false): bool
    {
        foreach ($haystack as $item) {

            if (!$strict && is_string($needle) && (is_float($item) || is_int($item))) {
                $item = (string)$item;
            }

            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }

    public function checkSailburuada($username): array
    {
        $ip = $this->ip;
        $basedn = $this->basedn;
        $passwd = $this->passwd;
        $resp = [];

        $srv = "ldap://$ip:389";
        $ldap = Ldap::create('ext_ldap', ['connection_string' => $srv]);
        $ldap->bind('CN=izfeprint,CN=Users,DC=pasaia,DC=net', $passwd);

        // Sailburuada
        $gFilter = "(&(samAccountName=$username)(memberOf:1.2.840.113556.1.4.1941:=CN=Taldea-Sailburuak,CN=Users,DC=pasaia,DC=net))";
        $query = $ldap->query($basedn, $gFilter);
        $result = $query->execute();
        $resp['sailburuada'] = $result['count'];

        // Saila
        $gFilter = "(member:1.2.840.113556.1.4.1941:=cn=$username,cn=users,dc=pasaia,dc=net)";
        $query = $ldap->query($basedn, $gFilter);
        $result2 = $query->execute();

        /**
         * @var Entry $entry
         * @var int $key
         */
        foreach ($result2 as $key => $entry) {
            if ($key !== 'count') {
                $taldea = $entry->getAttribute('name')[0];
                if (str_starts_with($taldea, 'Saila')) {
                    $resp['saila'] = explode('-', $taldea)[1];
                }
            }
        }

        return $resp;
    }

    public function getLdapUserMembershipGroupsRecursivelyByUsername($username): array
    {
        $ip = $this->ip;
        $basedn = $this->basedn;
        $passwd = $this->passwd;
        $ldapSarbideak = [];
        $ldapRolak = [];
        $ldapSailak = [];
        $ldapTaldeak = [];
        $ldapApp = [];

        $srv = "ldap://$ip:389";
        $ldap = Ldap::create('ext_ldap', ['connection_string' => $srv]);
        $ldap->bind('CN=izfeprint,CN=Users,DC=pasaia,DC=net', $passwd);
        $gFilter = "(member:1.2.840.113556.1.4.1941:=cn=$username,cn=users,dc=pasaia,dc=net)";
        $query = $ldap->query($basedn, $gFilter);
        $allGroups = $query->execute();

        /**
         * @var int $key
         * @var Entry $group
         */
        foreach ($allGroups as $key => $group) {
            if ($key !== 'count') {
                $taldea = $group->getAttribute('name')[0];
                switch ($taldea) {
                    case str_starts_with($taldea, 'APP-'):
                        $ldapApp[] = $taldea;
                        break;
                    case str_starts_with($taldea, 'ROL-'):
                        $ldapRolak[] = $taldea;
                        break;
                    case str_starts_with($taldea, 'Saila-'):
                        $ldapSailak[] = $taldea;
                        break;
                    case str_starts_with($taldea, 'SARBIDE-'):
                        $ldapSarbideak[] = $taldea;
                        break;
                    case str_starts_with($taldea, 'TALDEA-'):
                        $ldapTaldeak[] = $taldea;
                        break;
                }
            }
        }

        return [
            'app' => $ldapApp,
            'rol' => $ldapRolak,
            'saila' => $ldapSailak,
            'sarbide' => $ldapSarbideak,
            'taldeak' => $ldapTaldeak,
        ];
    }
}
