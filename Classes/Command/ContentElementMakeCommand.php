<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command;

use Digitalwerk\Typo3ElementRegistryCli\ElementObjects\ContentElementObject;
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
        $this->contentElementObject->render();
    }

    public function afterMake(): void
    {
//        TODO: message about what need to change ('icon, preview image, fill template')
        parent::afterMake();
    }
}
