<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Utility\FieldsCreateCommandUtility;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Record
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements
 */
class Record
{
    /**
     * @param ElementObject $elementObject
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function execute(ElementObject $elementObject)
    {
        $extensionName = $elementObject->getExtensionName();
        $name = $elementObject->getName();
        $vendor = $elementObject->getVendor();

        $table = 'tx_' . str_replace('_', '', $extensionName) . '_domain_model_' . strtolower($name);
        $relativePathToModel = $extensionName . '/Classes/Domain/Model';
        $extensionNameInNameSpace = str_replace(' ','',ucwords(str_replace('_',' ',$extensionName)));
        $namespaceToModel = $vendor . '\\' . $extensionNameInNameSpace . '\Domain\Model';

        $elementObject->setFieldsSpacesInTcaColumn('        ');
        $elementObject->setTable($table);
        $elementObject->setInlineRelativePath($relativePathToModel);
        $elementObject->setModelNamespace($namespaceToModel);
        $elementObject->setTcaFieldsPrefix(false);
        $elementObject->setStaticName($name);

        $elementRender = GeneralUtility::makeInstance(ElementRender::class);
        $elementRender->setElement($elementObject);
        $elementRender->check()->recordCreateCommand();
        $elementRender->model()->recordTemplate();
        $elementRender->tca()->recordTemplate();
        $elementRender->icon()->copyRecordDefaultIcon();
        $elementRender->sqlDatabase()->recordFields();
        $elementRender->translation()->addFieldsTitleToTranslation(
            $elementObject->getTranslationPath()
        );
        $elementRender->translation()->addStringToTranslation(
            $elementObject->getTranslationPath(),
            $table,
            $elementObject->getTitle()
        );
        $elementRender->inline()->render();
        $elementRender->typo3Cms()->compareDatabase();
        $elementRender->typo3Cms()->fixFileStructure();
        $elementRender->typo3Cms()->clearCache();
        $elementObject->getOutput()
            ->writeln('<bg=green;options=bold>Record ' . $name . ' was created.</>');
    }
}
