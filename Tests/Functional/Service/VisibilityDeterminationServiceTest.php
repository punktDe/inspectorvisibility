<?php
declare(strict_types=1);

namespace PunktDe\InspectorVisibility\Tests\Functional\Service;

/*
 *  (c) 2019 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use Neos\Flow\Tests\FunctionalTestCase;
use PunktDe\InspectorVisibility\Service\VisibilityDeterminationService;

class VisibilityDeterminationServiceTest extends FunctionalTestCase
{
    /**
     * @var VisibilityDeterminationService
     */
    protected $visibilityDeterminationService;

    protected $testableSecurityEnabled = true;

    public function setUp()
    {
        parent::setup();
        $this->visibilityDeterminationService = $this->objectManager->get(VisibilityDeterminationService::class);
    }

    /**
     * @test
     */
    public function noMatchingRuleReturnsConfiguredValue()
    {
        $actual = $this->visibilityDeterminationService->determineHiddenState(true, 'PunktDe.InspectorVisibility:TestNode', 'does', 'not', 'match');
        $this->assertTrue($actual, 'Configured: true - returned false');

        $actual = $this->visibilityDeterminationService->determineHiddenState(false, 'PunktDe.InspectorVisibility:TestNode', 'does', 'not', 'match');
        $this->assertFalse($actual, 'Configured: false - returned true');
    }

    /**
     * @test
     */
    public function adminOptionHiddenForAnEditor()
    {
        $this->authenticateRoles(['PunktDe.InspectorVisibility:RestrictedEditor']);
        $actual = $this->visibilityDeterminationService->determineHiddenState(false, 'PunktDe.InspectorVisibility:TestNode', '', '', 'adminOption');
        $this->assertTrue($actual);
    }

    /**
     * @test
     */
    public function adminOptionNotHiddenForAnAdministrator()
    {
        $this->authenticateRoles(['Neos.Neos:Administrator']);
        $actual = $this->visibilityDeterminationService->determineHiddenState(true, 'PunktDe.InspectorVisibility:TestNode', '', '', 'adminOption');
        $this->assertFalse($actual);
    }
}
