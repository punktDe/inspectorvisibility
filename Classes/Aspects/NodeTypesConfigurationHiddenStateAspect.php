<?php
declare(strict_types=1);

namespace PunktDe\InspectorVisibility\Aspects;

/*
 *  (c) 2019 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;
use Neos\Flow\Security\Exception\NoSuchRoleException;
use Neos\Flow\Utility\Algorithms;
use PunktDe\InspectorVisibility\Service\VisibilityDeterminationService;

/**
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class NodeTypesConfigurationHiddenStateAspect
{

    /**
     * @Flow\Inject
     * @var VisibilityDeterminationService
     */
    protected $visibilityDeterminationService;

    /**
     * @var string
     */
    protected $noMatchIdentifier;

    /**
     * @Flow\Around("method(Neos\Neos\Service\NodeTypeSchemaBuilder->generateNodeTypeSchema())")
     * @param JoinPointInterface $joinPoint
     * @return array
     * @throws \Neos\Flow\Security\Exception
     * @throws NoSuchRoleException
     */
    public function adjustHiddenState(JoinPointInterface $joinPoint)
    {
        $schema = $joinPoint->getAdviceChain()->proceed($joinPoint);

        $nodeTypes = &$schema['nodeTypes'];
        $this->noMatchIdentifier = Algorithms::generateRandomString(10);

        foreach ($nodeTypes as $nodeTypeName => &$nodeTypeConfiguration) {
            $this->adjustProperties($nodeTypeName, $nodeTypeConfiguration);
            $this->adjustGroups($nodeTypeName, $nodeTypeConfiguration);
        }

        return $schema;
    }

    protected function adjustTabs(string $nodeTypeName, array &$nodeTypeConfiguration): void
    {
        // @TODO: Use tab hidden property, once it is available https://github.com/neos/neos-ui/issues/2285

        if (!isset($nodeTypeConfiguration['ui']['inspector']['tabs']) || !is_array($nodeTypeConfiguration['ui']['inspector']['tabs'])) {
            return;
        }

        foreach ($nodeTypeConfiguration['ui']['inspector']['tabs'] as $tabName => &$tabConfiguration) {
            $adjustedHiddenState = $this->visibilityDeterminationService->determineHiddenState(
                false,
                $nodeTypeName,
                $tabName,
                'group-should-not-match-' . $this->noMatchIdentifier,
                'property-should-not-match-' . $this->noMatchIdentifier
            );

            if($adjustedHiddenState === true) {
                $tabConfiguration = [];
            }
        }
    }

    /**
     * @param string $nodeTypeName
     * @param array $nodeTypeConfiguration
     * @throws NoSuchRoleException
     * @throws \Neos\Flow\Security\Exception
     */
    protected function adjustGroups(string $nodeTypeName, array &$nodeTypeConfiguration): void
    {
        // @TODO: Use group hidden property, once it is available https://github.com/neos/neos-ui/issues/2285

        if (!isset($nodeTypeConfiguration['ui']['inspector']['groups']) || !is_array($nodeTypeConfiguration['ui']['inspector']['groups'])) {
            return;
        }

        foreach ($nodeTypeConfiguration['ui']['inspector']['groups'] as $groupName => &$groupConfiguration) {
            $adjustedHiddenState = $this->visibilityDeterminationService->determineHiddenState(
                false,
                $nodeTypeName,
                $groupConfiguration['tab'] ?? 'tab-should-not-match-' . $this->noMatchIdentifier,
                $groupName,
                'property-should-not-match-' . $this->noMatchIdentifier
            );

            if($adjustedHiddenState === true) {
                $groupConfiguration['tab'] = 'non-existent-tab-' . $this->noMatchIdentifier;
            }
        }
    }

    /**
     * @param string $nodeTypeName
     * @param array $nodeTypeConfiguration
     * @throws \Neos\Flow\Security\Exception
     * @throws NoSuchRoleException
     */
    protected function adjustProperties(string $nodeTypeName, array &$nodeTypeConfiguration): void
    {
        if (!isset($nodeTypeConfiguration['properties']) || !is_array($nodeTypeConfiguration['properties'])) {
            return;
        }

        foreach ($nodeTypeConfiguration['properties'] as $propertyName => $propertyConfiguration) {
            if (!isset($propertyConfiguration['ui']['inspector'])) {
                continue;
            }

            // @Todo: We need to determine the tab from the group here
            $groupName = $propertyConfiguration['ui']['inspector']['group'] ?? '--group-not-set-' . $this->noMatchIdentifier;
            $tabName = $nodeTypeConfiguration['ui']['inspector']['groups'][$groupName]['tab'] ?? '--tab-not-set-' . $this->noMatchIdentifier;

            $configuredHiddenState = $nodeTypeConfiguration['properties'][$propertyName]['ui']['inspector']['hidden'] ?? null;

            $adjustedHiddenState = $this->visibilityDeterminationService->determineHiddenState(
                $configuredHiddenState,
                $nodeTypeName,
                $tabName,
                $groupName,
                $propertyName
            );

            if ($adjustedHiddenState !== null) {
                $nodeTypeConfiguration['properties'][$propertyName]['ui']['inspector']['hidden'] = $adjustedHiddenState;
            }
        }
    }
}
