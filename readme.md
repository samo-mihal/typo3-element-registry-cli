# <img src="https://github.com/samo-mihal/typo3-element-registry-cli/raw/master/Resources/Public/Icons/Extension.svg?sanitize=true" width="40" height="40"/> Typo3 element registry CLI
Create a new elements (like Content element, Page type, etc..) with CLI.

## Install
Install extension via composer `composer require digitalwerk/typo3-element-registry-cli` and activate it in Extension module

## Setup
###Extension settings
After activating extension, you have to fill in extension settings.

####General
- vendor

####Content element
- classExtend (optional)
- modelExtend (optional)
- classTemplatePath (optional)
- modelTemplatePath (optional)
- templateTemplatePath (optional)

####Page type
- typoScriptConstantsPath (required)
  - path to typoscript constants (Eg. EXT:{extension}/Configuration/TypoScript/constants.typoscript)
- utilityPath
    - Path to utility class
    - Utility class must contain addPageDoktype(int $doktype) static function and addTcaDoktype(int $doktype, string $position = '') static function
- modelExtend (optional)
- modelTemplatePath (optional)

####Plugin
- controllerExtend (optional)

####Record
- modelTemplatePath (optional)
- tcaTemplatePath (optional)

###Markers
####/** Registered icons */
- Where: EXT:{extension}/ext_localconf.php

####/** Plugins configuration */
- Where: EXT:{extension}/ext_localconf.php

####/** Register page doktypes */
- Where: EXT:{extension}/ext_tables.php

####/** Add page doktypes */
- Where: EXT:{extension}/Configuration/TCA/Overrides/pages.php

#####Page types
- Where: EXT:{extension}/Configuration/TypoScript/constants.typoscript
