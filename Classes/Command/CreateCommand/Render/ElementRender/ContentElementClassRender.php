<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\PHPClassBuilder\Object\PHPClassObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ContentElementClassRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class ContentElementClassRender extends AbstractRender
{
    /**
     * @var FieldsRender
     */
    protected $fieldsRender = null;

    /**
     * @var PHPClassObject
     */
    protected $contentElementClass = null;

    /**
     * ContentElementClassRender constructor.
     * @param ElementRender $elementRender
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $elementRender);

        $this->contentElementClass = new PHPClassObject($this->element->getContentElementClassPath());
        $this->contentElementClass->setStrictMode(true);
        $this->contentElementClass->setName($this->element->getName());
        $this->contentElementClass->setNameSpace($this->element->getContentElementClassNameSpace());
        $this->contentElementClass->setExtendsOrImplements(
            'extends \\' . $this->element->getContentElementExtendClass()
        );
        $this->contentElementClass->setComment(
            '/**
 * Class ' . $this->element->getName() . '
 * @package ' . $this->element->getContentElementClassNameSpace() . '
 */'
        );
    }

    /**
     * ContentElementClass destructor.
     */
    public function __destruct()
    {
        $this->contentElementClass->render();
    }

    /**
     * @return void
     */
    public function columnMapping(): void
    {
        $fieldsToClassMapping = $this->fieldsRender->fieldsToClassMapping();

        if ($fieldsToClassMapping) {
            if ($this->contentElementClass->contains()->variable('columnsMapping')) {
                $columnsMappingValue = explode(
                    "\n",
                    $this->contentElementClass->get()->variable('columnsMapping')->getValue()
                );
                $columnsMappingValue = $this->importStringRender->arrayInsertAfter(
                    $columnsMappingValue,
                    0,
                    [
                        $this->contentElementClass->getTabSpaces() .
                        $this->contentElementClass->getTabSpaces() .
                        $fieldsToClassMapping . ','
                    ]
                );
                $this->contentElementClass->edit()->variable('columnsMapping')
                    ->setValue(implode("\n", $columnsMappingValue));
            } else {
                $columnsMappingValue = '[' . "\n" .
                    $this->contentElementClass->getTabSpaces() . $this->contentElementClass->getTabSpaces() .
                    $fieldsToClassMapping . "\n" .
                    $this->contentElementClass->getTabSpaces() . ']';

                $columnsMappingComment = '/**' . "\n" .
                    $this->contentElementClass->getTabSpaces() . ' * @var array' . "\n" .
                    $this->contentElementClass->getTabSpaces() . ' */';

                $this->contentElementClass->addVariable()
                    ->setName('columnsMapping')
                    ->setType('protected')
                    ->setComment($columnsMappingComment)
                    ->setValue($columnsMappingValue);
            }
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     * @return void
     */
    public function columnOverride(): void
    {
        $fieldsToColumnsOverrides = $this->fieldsRender->fieldsToColumnsOverrides();

        if ($fieldsToColumnsOverrides) {
            if ($this->contentElementClass->contains()->function('getColumnsOverrides')) {
                $getColumnsOverridesValue = explode(
                    "\n",
                    $this->contentElementClass->get()->function('getColumnsOverrides')->getContent()
                );
                $getColumnsOverridesValue = $this->importStringRender->arrayInsertAfter(
                    $getColumnsOverridesValue,
                    1,
                    [
                        $this->contentElementClass->getTabSpaces() .
                        $this->contentElementClass->getTabSpaces() .
                        $this->contentElementClass->getTabSpaces() .
                        $fieldsToColumnsOverrides
                    ]
                );
                $this->contentElementClass->edit()->function('getColumnsOverrides')
                    ->setContent(implode("\n", $getColumnsOverridesValue));
            } else {
                $getColumnsOverridesValue = '{' . "\n" .
                    $this->contentElementClass->getTabSpaces() . $this->contentElementClass->getTabSpaces() .
                    'return [' . "\n" .
                    $this->contentElementClass->getTabSpaces() . $this->contentElementClass->getTabSpaces() .
                    $this->contentElementClass->getTabSpaces() . $fieldsToColumnsOverrides . "\n" .
                    $this->contentElementClass->getTabSpaces() . $this->contentElementClass->getTabSpaces() .
                    '];' . "\n" .
                    $this->contentElementClass->getTabSpaces() . '}';

                $columnsMappingComment = '/**' . "\n" .
                    $this->contentElementClass->getTabSpaces() . ' * @return array' . "\n" .
                    $this->contentElementClass->getTabSpaces() . ' */';

                $this->contentElementClass->addFunction()
                    ->setName('getColumnsOverrides')
                    ->setType('public function')
                    ->setArgumentsAndDescription('(): array')
                    ->setComment($columnsMappingComment)
                    ->setContent($getColumnsOverridesValue);
            }
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function palette()
    {
        $fieldsToPalette = $this->fieldsRender->fieldsToPalette();
        if ($fieldsToPalette) {
            if ($this->contentElementClass->contains()->function('__construct')) {
                $constructValue = explode(
                    "\n",
                    $this->contentElementClass->get()->function('__construct')->getContent()
                );
                $pos = strpos($constructValue[4], "'") + 1;
                $constructValue[4] = substr($constructValue[4], 0, $pos) .
                    $fieldsToPalette . ',' .
                    substr($constructValue[4], $pos);
                $this->contentElementClass->edit()->function('__construct')
                    ->setContent(implode("\n", $constructValue));
            } else {
                $constructValue = '{' . "\n" .
                    $this->contentElementClass->getTabSpaces() . $this->contentElementClass->getTabSpaces() .
                    'parent::__construct();' . "\n" .
                    $this->contentElementClass->getTabSpaces() . $this->contentElementClass->getTabSpaces() .
                    '$this->addPalette(' . "\n" .
                    $this->contentElementClass->getTabSpaces() . $this->contentElementClass->getTabSpaces() .
                    $this->contentElementClass->getTabSpaces() .
//                    Palette name
                    "'default'," . "\n" .
                    $this->contentElementClass->getTabSpaces() . $this->contentElementClass->getTabSpaces() .
                    $this->contentElementClass->getTabSpaces() .
                    "'" . $fieldsToPalette . "'\n" .
                    $this->contentElementClass->getTabSpaces() . $this->contentElementClass->getTabSpaces() .
                    ');' . "\n" .
                    $this->contentElementClass->getTabSpaces() . '}';

                $columnsMappingComment = '/**' . "\n" .
                    $this->contentElementClass->getTabSpaces() . ' * ' . $this->element->getName() . ' constructor.' . "\n" .
                    $this->contentElementClass->getTabSpaces() . ' * @throws \Exception' . "\n" .
                    $this->contentElementClass->getTabSpaces() . ' */';

                $this->contentElementClass->addFunction()
                    ->setName('__construct')
                    ->setType('public function')
                    ->setArgumentsAndDescription('()')
                    ->setComment($columnsMappingComment)
                    ->setContent($constructValue);
            }
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function template()
    {
        $this->columnMapping();
        $this->palette();
        $this->columnOverride();
    }
}
