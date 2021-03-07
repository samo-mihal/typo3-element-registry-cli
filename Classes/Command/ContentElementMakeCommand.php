<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command;

use Digitalwerk\Typo3ElementRegistryCli\ElementObjects\ContentElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Utility\TranslationUtility;
use Symfony\Component\Console\Question\ChoiceQuestion;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RunCreateElementCommand
 * @package Digitalwerk\Typo3ElementRegistryCli\Command
 */
class ContentElementMakeCommand extends AbstractMakeCommand
{
    /**
     * @var array
     */
    protected $requiredFiles = [
        'EXT:{extension}/Resources/Private/Language/locallang_db.xlf'
    ];

    /**
     * @var string
     */
    protected $table = 'tt_content';

    /**
     * @var ContentElementObject
     */
    protected $contentElementObject = null;

    /**
     * @return void
     */
    public function beforeMake(): void
    {
        if (ExtensionManagementUtility::isLoaded('content_element_registry') === false) {
            throw new \InvalidArgumentException('Extension content_element_registry is not loaded.');
        }
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('content_element_registry');
        $contentElementsPaths = explode(',', $extensionConfiguration['contentElementsPaths']);
        if (!empty($contentElementsPaths)) {
            if (count($contentElementsPaths) > 1) {
                $extensions = [];

                foreach ($contentElementsPaths as $contentElementsPath) {
                    $extensions[] = substr(explode('/', $contentElementsPath)[0], 4);
                }

                $this->extension = $this->questionHelper->ask($this->input, $this->output, new ChoiceQuestion(
                    'Please select extension.',
                    $extensions,
                    0
                ));
            } else {
                $this->extension = substr(explode('/', $contentElementsPaths[0])[0], 4);
            }
        }

        $this->contentElementObject = (new ContentElementObject($this->input, $this->output, $this->questionHelper));
        $this->contentElementObject->questions();

        parent::beforeMake();
    }

    /**
     * @return void
     */
    public function make(): void
    {
        $elementId = str_replace('_', '', $this->extension) . '_' . strtolower($this->contentElementObject->getName());

//        TODO: Class default template with default extend class
//        TODO: Model default template with default extend class
//        TODO: Template

        /** Write title and description to locallang */
        TranslationUtility::addStringToTranslation(
            'EXT:' . $this->extension . '/Resources/Private/Language/locallang_db.xlf',
            $this->table . '.' . $elementId . '.title',
            $this->contentElementObject->getTitle()
        );
        TranslationUtility::addStringToTranslation(
            'EXT:' . $this->extension . '/Resources/Private/Language/locallang_db.xlf',
            $this->table . '.' . $elementId . '.description',
            $this->contentElementObject->getDescription()
        );

        /** Copy icon and preview image */
        copy(
            GeneralUtility::getFileAbsFileName('EXT:content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg'),
            GeneralUtility::getFileAbsFileName(
                'EXT:' . $this->extension . '/Resources/Public/Icons/ContentElement/' . $elementId . '.svg'
            )
        );
        copy(
            GeneralUtility::getFileAbsFileName(
                'EXT:content_element_registry/Resources/Public/Images/NewContentElement1.png'
            ),
            GeneralUtility::getFileAbsFileName(
                'EXT:' . $this->extension . '/Resources/Public/Images/ContentElementPreviews/' .
                'common_' . $elementId . '.png'
            )
        );
    }

    /**
     * @return void
     */
    public function afterMake(): void
    {
        $this->output->writeln('<bg=red;options=bold>Change content element icon</>');
        $this->output->writeln('<bg=red;options=bold>Change content element preview image</>');
        parent::afterMake();
    }
}
