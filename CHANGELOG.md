# Change Log
 All notable changes to this project will be documented in this file.
 
## [Unreleased]
 
## [v2.0.0]
#### Added
- Added file phpcs.xml
 
#### Changed
- Changed code style to PSR-2
- addInstitution URL nad email in disco-tpl.php are loaded from a config file
- Templates are included from module perun
 
## [v1.4.0]
#### Added
- Added support for pass selected IdP from SP in AuthnContextClassRef attribute.
   
    - It's required add this line into module_perun.php config file 
    <pre>
    'disco.removeAuthnContextClassRefPrefix' => 'urn:cesnet:proxyidp:',
    </pre> 

#### Changed
- Social Idps are not shown when adding institution
 
## [v1.3.0]
#### Added
- Added support for MFA
 
## [v1.2.2]
#### Removed
- Removed present_attributes() method from consentform.php

## [v1.2.1]
#### Fixed
- Fixed requirements in composer.json
 
## [v1.2.0]
#### Added
- Possibility to show a warning in disco-tpl
 
#### Changed
- Updated Readme
 
## [v1.1.0]
#### Changed
- Changed the URL to AddInstitution App
 
## [v1.0.2]
#### Changed
- Whole module now uses a dictionary
 
## [v1.0.1]
#### Changed
- Filling email is now required for reporting error
- Changed help-block text for email in report error form
- Fixed changelog

## [v1.0.0]
#### Added
- Changelog

[Unreleased]: https://github.com/elixirhub/elixir-aai-proxy-idp-template/tree/master
[v2.0.0]: https://github.com/elixirhub/elixir-aai-proxy-idp-template/tree/v2.0.0
[v1.4.0]: https://github.com/elixirhub/elixir-aai-proxy-idp-template/tree/v1.4.0
[v1.3.0]: https://github.com/elixirhub/elixir-aai-proxy-idp-template/tree/v1.3.0
[v1.2.2]: https://github.com/elixirhub/elixir-aai-proxy-idp-template/tree/v1.2.2
[v1.2.1]: https://github.com/elixirhub/elixir-aai-proxy-idp-template/tree/v1.2.1
[v1.2.0]: https://github.com/elixirhub/elixir-aai-proxy-idp-template/tree/v1.2.0
[v1.1.0]: https://github.com/elixirhub/elixir-aai-proxy-idp-template/tree/v1.1.0
[v1.0.2]: https://github.com/elixirhub/elixir-aai-proxy-idp-template/tree/v1.0.2
[v1.0.1]: https://github.com/elixirhub/elixir-aai-proxy-idp-template/tree/v1.0.1
[v1.0.0]: https://github.com/elixirhub/elixir-aai-proxy-idp-template/tree/v1.0.0