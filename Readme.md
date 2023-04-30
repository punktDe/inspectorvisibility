# PunktDe.InspectorVisibility

[![Latest Stable Version](https://poser.pugx.org/punktde/inspectorvisibility/v/stable)](https://packagist.org/packages/punktde/inspectorvisibility) [![Total Downloads](https://poser.pugx.org/punktde/inspectorvisibility/downloads)](https://packagist.org/packages/punktde/inspectorvisibility)

This package allows you to configure the visibility of inspector elements, like properties, groups and tabs using policies. With that you can configure the visibility of these elements according to the users roles.

**CAUTION: Although this is done with policies, this is not a security feature!**

If you prevent a user from editing nodes, you aditionally need to define policies with an `EditNodePropertyPrivilege` for example.

## Installation

The installation is done with composer:

```bash
composer require punktde/inspectorvisibility
```

## Usage

The matcher can be defined using standard eel. The following properties to match for are available:

* nodeTypeName
* tabName
* groupName
* propertyName

If no policy is matching for a role, the configured visibility is used. Same, if a permission is set to `ABSTAIN`.

### Examples

#### Example `Policy.yaml`

```yaml
privilegeTargets:

  'PunktDe\InspectorVisibility\Security\Authorization\Privilege\InspectorVisibilityPrivilege':
    'PunktDe.InspectorVisibility:AdminFields':
      matcher: "${tabName == 'meta' && groupName == 'nodeInfo'}"

roles:
  'Neos.Neos:Editor':
    privileges:
      -
        privilegeTarget: 'PunktDe.InspectorVisibility:AdminFields'
        permission: DENY
```

#### Matcher Examples

* Adress all *uriPathSegment* properties: `matcher: "${propertyName == 'uriPathSegment'}`
* Adress all *meta* tabs of all nodeTypes `"${tabName == 'meta'}"`
* Address all *title* fields of a specific type `matcher: "${nodeTypeName == 'Neos.Demo:Registration' && propertyName == 'title'}"`




