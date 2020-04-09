<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ContentElementClass
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class ContentElementClassRender
{
    /**
     * @var RenderCreateCommand
     */
    protected $render = null;

    /**
     * @var FieldsRender
     */
    protected $fieldsRender = null;

    /**
     * ContentElementClass constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $render);
    }

    /**
     * @return string|null
     */
    public function getColumnMapping()
    {
        $fieldsToClassMapping = $this->fieldsRender->fieldsToClassMapping();
        if ($fieldsToClassMapping) {
            return
'    /**
     * @var array
     */
    protected $columnsMapping = [
        ' . $fieldsToClassMapping . '
    ];';
        } else {
            return null;
        }
    }

    /**
     * @return string|null
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getColumnOverride()
    {
        $fieldsToColumnsOverrides = $this->fieldsRender->fieldsToColumnsOverrides();
        if ($fieldsToColumnsOverrides) {
            return
'    /**
     * @return array
     */
    public function getColumnsOverrides()
    {
        return [
            ' . $fieldsToColumnsOverrides . '
        ];
    }';
        } else {
            return null;
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function template()
    {
        $vendor = $this->render->getVendor();
        $extensionName = str_replace(' ','',ucwords(str_replace('_',' ', $this->render->getExtensionName())));
        $template[] = '<?php';
        $template[] = 'declare(strict_types=1);';
        $template[] = 'namespace ' . $vendor . '\\' . $extensionName . '\ContentElement;';
        $template[] = '';
        $template[] = 'use Digitalwerk\ContentElementRegistry\ContentElement\AbstractContentElementRegistryItem;';
        $template[] = '';
        $template[] = '/**';
        $template[] = ' * Class ' . $this->render->getName();
        $template[] = ' * @package ' . $vendor . '\\' . $extensionName . '\ContentElement';
        $template[] = ' */';
        $template[] = 'class ' . $this->render->getName() . ' extends AbstractContentElementRegistryItem';
        $template[] = '{';

        $columnMapping = $this->getColumnMapping();
        if ($columnMapping) {
            $template[] = $columnMapping;
            $template[] = '';
        }

        $template[] = '    /**';
        $template[] = '     * ' . $this->render->getName() . ' constructor.';
        $template[] = '     * @throws \Exception';
        $template[] = '     */';
        $template[] = '    public function __construct()';
        $template[] = '    {';
        $template[] = '        parent::__construct();';

        $fieldsToPalette = $this->fieldsRender->fieldsToPalette();
        if ($fieldsToPalette) {
            $template[] = '        $this->addPalette(';
            $template[] = '            \'default\',';
            $template[] = "            '" . $fieldsToPalette . "'";
            $template[] = '        );';
        }
        $template[] = '    }';

        $columnOverride = $this->getColumnOverride();
        if ($columnOverride) {
            $template[] = '';
            $template[] = $columnOverride;
        }
        $template[] = '}';


        file_put_contents(
            'public/typo3conf/ext/' . $this->render->getExtensionName() . '/Classes/ContentElement/' . $this->render->getName() . '.php',
            implode("\n", $template)
        );
    }
}
