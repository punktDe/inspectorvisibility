<?php
declare(strict_types=1);

namespace PunktDe\InspectorVisibility\Security\Authorization\Privilege;

/*
 *  (c) 2019 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Eel\Utility;
use Neos\Eel\EelEvaluatorInterface;
use Neos\Flow\Security\Authorization\Privilege\AbstractPrivilege;
use Neos\Flow\Security\Authorization\Privilege\PrivilegeSubjectInterface;
use Neos\Flow\Security\Exception\InvalidPrivilegeTypeException;

class InspectorVisibilityPrivilege extends AbstractPrivilege
{
    /**
     * @var array
     * @Flow\InjectConfiguration(path="defaultContext")
     */
    protected $defaultContextConfiguration;

    /**
     * @var EelEvaluatorInterface
     * @Flow\Inject(lazy=false)
     */
    protected $eelEvaluator;

    /**
     * Returns true, if this privilege covers the given subject
     *
     * @param PrivilegeSubjectInterface $subject
     * @return boolean
     * @throws InvalidPrivilegeTypeException if the given $subject is not supported by the privilege
     * @throws \Neos\Eel\Exception
     */
    public function matchesSubject(PrivilegeSubjectInterface $subject)
    {
        if (!$subject instanceof InspectorVisibilitySubject) {
            throw new InvalidPrivilegeTypeException(sprintf('Invalid subject type %s, only InspectorVisibilitySubject supported.', get_class($subject)), 1547885501);
        }
        /** @var InspectorVisibilitySubject $subject */
        $result = Utility::evaluateEelExpression($this->matcher, $this->eelEvaluator, ['nodeTypeName' => $subject->getNodeTypeName(), 'tabName' => $subject->getTabName(), 'groupName' => $subject->getGroupName(), 'propertyName' => $subject->getPropertyName()], $this->defaultContextConfiguration);
        return $result;
    }
}
