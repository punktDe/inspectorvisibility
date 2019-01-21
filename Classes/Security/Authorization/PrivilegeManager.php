<?php
declare(strict_types=1);

namespace PunktDe\InspectorVisibility\Security\Authorization;

/*
 *  (c) 2019 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use Neos\Flow\Security\Authorization\PrivilegeManager as OriginalFlowPrivilegeManager;
use Neos\Flow\Security\Authorization\PrivilegePermissionResult;


/**
 * This implementation exposes the PrivilegePermissionResult to also
 * evaluate the abstains. Should go to the core.
 */
class PrivilegeManager extends OriginalFlowPrivilegeManager
{
    /**
     * @param string $privilegeType
     * @param $subject
     * @param string $reason
     * @return PrivilegePermissionResult
     * @throws \Neos\Flow\Security\Exception
     * @throws \Neos\Flow\Security\Exception\NoSuchRoleException
     */
    public function getPrivilegePermissionResult(string $privilegeType, $subject, string &$reason = ''): PrivilegePermissionResult
    {
        $availablePrivileges = array_reduce($this->securityContext->getRoles(), $this->getPrivilegeByTypeReducer($privilegeType), []);
        $effectivePrivileges = array_filter($availablePrivileges, $this->getPrivilegeSubjectFilter($subject));

        /** @var PrivilegePermissionResult $result */
        $result = array_reduce($effectivePrivileges, [$this, 'applyPrivilegeToResult'], new PrivilegePermissionResult());

        $effectivePrivilegeIdentifiersWithPermission = $result->getEffectivePrivilegeIdentifiersWithPermission();

        $reason = sprintf('Evaluated following %d privilege target(s):' . chr(10) . '%s' . chr(10) . '(%d granted, %d denied, %d abstained)',
            count($effectivePrivilegeIdentifiersWithPermission),
            implode(chr(10), $effectivePrivilegeIdentifiersWithPermission),
            $result->getGrants(),
            $result->getDenies(),
            $result->getAbstains()
        );

        return $result;
    }
}
