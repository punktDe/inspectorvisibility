<?php
declare(strict_types=1);

namespace PunktDe\InspectorVisibility\Service;

/*
 *  (c) 2019 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use Neos\Flow\Annotations as FLow;
use PunktDe\InspectorVisibility\Security\Authorization\Privilege\InspectorVisibilityPrivilege;
use PunktDe\InspectorVisibility\Security\Authorization\Privilege\InspectorVisibilitySubject;
use PunktDe\InspectorVisibility\Security\Authorization\PrivilegeManager;

/**
 * @Flow\Scope("singleton")
 */
class VisibilityDeterminationService
{

    /**
     * @Flow\Inject
     * @var PrivilegeManager
     */
    protected $privilegeManager;

    /**
     * @param bool $originalHiddenState
     * @param string $nodeTypeName
     * @param string $tabName
     * @param string $groupName
     * @param string $propertyName
     * @return bool If the target should be hidden
     * @throws \Neos\Flow\Security\Exception
     * @throws \Neos\Flow\Security\Exception\NoSuchRoleException
     */
    public function determineHiddenState(?bool $originalHiddenState, string $nodeTypeName, string $tabName, string $groupName, string $propertyName): ?bool
    {
        $result = $this->privilegeManager->getPrivilegePermissionResult(
            InspectorVisibilityPrivilege::class, new InspectorVisibilitySubject($nodeTypeName, $tabName, $groupName, $propertyName)
        );

        if ($result->getDenies() > 0) {
            return true;
        }

        if ($result->getGrants() > 0) {
            return false;
        }

        return $originalHiddenState;
    }
}
