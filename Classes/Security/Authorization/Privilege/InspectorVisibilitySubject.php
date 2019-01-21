<?php
declare(strict_types=1);

namespace PunktDe\InspectorVisibility\Security\Authorization\Privilege;

/*
 *  (c) 2019 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use Neos\Flow\Security\Authorization\Privilege\PrivilegeSubjectInterface;

class InspectorVisibilitySubject implements PrivilegeSubjectInterface
{
    /**
     * @var string
     */
    protected $nodeTypeName;

    /**
     * @var string
     */
    protected $tabName;

    /**
     * @var string
     */
    protected $groupName;

    /**
     * @var string
     */
    protected $propertyName;

    /**
     * @param string $nodeTypeName
     * @param string $tabName
     * @param string $groupName
     * @param string $propertyName
     */
    public function __construct(string $nodeTypeName, string $tabName, string $groupName, string $propertyName)
    {
        $this->nodeTypeName = $nodeTypeName;
        $this->tabName = $tabName;
        $this->groupName = $groupName;
        $this->propertyName = $propertyName;
    }

    /**
     * @return string
     */
    public function getNodeTypeName(): string
    {
        return $this->nodeTypeName;
    }

    /**
     * @return string
     */
    public function getTabName(): string
    {
        return $this->tabName;
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupName;
    }

    /**
     * @return string
     */
    public function getPropertyName(): string
    {
        return $this->propertyName;
    }
}
