## [5.1.3](https://github.com/CESNET/elixir-aai-proxy-idp-template/compare/v5.1.2...v5.1.3) (2022-07-22)


### Bug Fixes

* **deps:** upgrade module perun to v 9+ ([bb15e84](https://github.com/CESNET/elixir-aai-proxy-idp-template/commit/bb15e8487553fc38e500b9241a2a09eb16260ea8))

## [5.1.2](https://github.com/CESNET/elixir-aai-proxy-idp-template/compare/v5.1.1...v5.1.2) (2022-07-22)


### Bug Fixes

* **deps:** bump proxystats dependedcy to v8.0.0 and higher ([c7cd1f2](https://github.com/CESNET/elixir-aai-proxy-idp-template/commit/c7cd1f2bd439887c77da7b591b00f9defaccf286))

## [5.1.1](https://github.com/CESNET/elixir-aai-proxy-idp-template/compare/v5.1.0...v5.1.1) (2022-07-01)


### Bug Fixes

* 🐛 Fix EMBL displaying of GDPR message in consent ([6b6cf1f](https://github.com/CESNET/elixir-aai-proxy-idp-template/commit/6b6cf1f9ab764319e97dacef6d6ae3dd7da0a385))

# [5.1.0](https://github.com/CESNET/elixir-aai-proxy-idp-template/compare/v5.0.4...v5.1.0) (2022-07-01)


### Features

* 🎸 Displaying statistics ([75f43c3](https://github.com/CESNET/elixir-aai-proxy-idp-template/commit/75f43c3a2ce44f1b886bbf743b37f5b478f9c945))

## [5.0.4](https://github.com/CESNET/elixir-aai-proxy-idp-template/compare/v5.0.3...v5.0.4) (2022-04-29)


### Bug Fixes

* 🐛 Fixed missing Head inject ([7f4bbd4](https://github.com/CESNET/elixir-aai-proxy-idp-template/commit/7f4bbd42c24bf7b8880cd971ed61ec6bc1ab19e2))

## [5.0.3](https://github.com/CESNET/elixir-aai-proxy-idp-template/compare/v5.0.2...v5.0.3) (2022-04-22)


### Bug Fixes

* 🐛 Fix privacyIdea template filename ([4265871](https://github.com/CESNET/elixir-aai-proxy-idp-template/commit/4265871040a57b1f7b2e8593b12b1c65a849f171))

## [5.0.2](https://github.com/CESNET/elixir-aai-proxy-idp-template/compare/v5.0.1...v5.0.2) (2022-04-13)


### Bug Fixes

* 🐛 Added missing consent withdrawal contact ([fb8242d](https://github.com/CESNET/elixir-aai-proxy-idp-template/commit/fb8242d01a3d717274f43baad6cc6ea3ae384915))

## [5.0.1](https://github.com/CESNET/elixir-aai-proxy-idp-template/compare/v5.0.0...v5.0.1) (2022-04-13)


### Bug Fixes

* 🐛 Fix consent collapse ([e34cf2e](https://github.com/CESNET/elixir-aai-proxy-idp-template/commit/e34cf2e947c01b259230afa606a4feb1f86983bb))

# [5.0.0](https://github.com/CESNET/elixir-aai-proxy-idp-template/compare/v4.0.0...v5.0.0) (2022-04-13)


### Features

* 🎸LS AAI design ([0042cc0](https://github.com/CESNET/elixir-aai-proxy-idp-template/commit/0042cc0b7747e7c43a3c3ab9c928f70c87d5743b))


### BREAKING CHANGES

* dropped ELIXIR design, using LS design from now on.

# [4.0.0](https://github.com/CESNET/elixir-aai-proxy-idp-template/compare/v3.0.1...v4.0.0) (2022-03-14)


### Features

* 🎸 Remove CSC MFA related functionality ([329eb78](https://github.com/CESNET/elixir-aai-proxy-idp-template/commit/329eb784483b2b5fb2125e5b2971efffa26daf04))


### BREAKING CHANGES

* 🧨 Removed CSC MFA related functionality

## [3.0.1](https://github.com/CESNET/elixir-aai-proxy-idp-template/compare/v3.0.0...v3.0.1) (2022-03-10)


### Bug Fixes

* 🐛 Fix name of loginForm file ([f988ea3](https://github.com/CESNET/elixir-aai-proxy-idp-template/commit/f988ea31422286335e7e3242b8841559b51c14d3))

# 1.0.0 (2022-03-10)


### Bug Fixes

* 🐛 Fix ECS check ([0bc2d67](https://github.com/elixirhub/elixir-aai-proxy-idp-template/commit/0bc2d67acabf1d01c753c0dc12d89305807b6806))
* 🐛 Fix ECS new style ([5eee8f3](https://github.com/elixirhub/elixir-aai-proxy-idp-template/commit/5eee8f302a7288835d8ca707b3ffb8385a35c298))


### Features

* privacyidea ([680a9ae](https://github.com/elixirhub/elixir-aai-proxy-idp-template/commit/680a9aea941bf91886c698476aea4fad99de0c42))

# Change Log
 All notable changes to this project will be documented in this file.
 
## [Unreleased]

## [v2.2.0]
#### Changed
- Allow use SSP module Perun in version v4.x

## [v2.1.0]
#### Added
- Added process filter for MFA using CSC MFA OIDC server
 
#### Changed
- Using of short array syntax ([] instead of array())
- Using imports instead of qualified names
- Removed warning template - it is no longer needed here because it was moved to module perun

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
[v2.2.0]: https://github.com/elixirhub/elixir-aai-proxy-idp-template/tree/v2.2.0
[v2.1.0]: https://github.com/elixirhub/elixir-aai-proxy-idp-template/tree/v2.1.0
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
