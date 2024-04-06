<?php
/*
 * abavo_form
 *
 * @copyright   2017 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\Examples\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use Abavo\AbavoForm\Domain\Model\Form;
use Abavo\Example\Domain\Repository\YourRepository;
/**
 * Example class
 *
 * - to show how it works to overwrite properties and its valdiations.
 *
 * - register your class at $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['formmodels'][] in the ext_localconf.php of your extension.
 *   Then you are able to choose them in the PlugIn-Flexform.
 *
 * - By TYPO3-Core the validators in this namespace availible: \TYPO3\CMS\Extbase\Validation\Validator\*
 *
 * - To validating in the domain model with an own validator class take a look
 *   https://docs.typo3.org/typo3cms/ExtbaseFluidBook/9-CrosscuttingConcerns/2-validating-domain-objects.html#validating-in-the-domain-model-with-an-own-validator-class
 *
 * - You are also able to add database fields/properties (Be sure you have defined a property mapping by TypoScript or TCA configurator).
 */
class FormExtendsExample extends Form
{
    // If you want to overwrite the pre-selection you change the IsoCodeA3
    public const DEFAULT_COUNTRY               = 'DEU'; //by IsoCodeA3 https://de.wikipedia.org/wiki/ISO-3166-1-Kodierliste
    // If you need to allow extended properties explicit for validation (egg. a property is not rendered by a f:form viewhelper) in some use case, you able to allow by this setting.
    public const ALLOW_EXT_PROPERTIES_EXPLICIT = false;
    // Should the form after creating reseted in session?
    public const RESET_AFTER_CREATE            = true;

    /**
     * RepositoryClass
     *
     * Change this if your extended model is mapped not mapped to the default table to able the unique_id calculation. In other case you should remove this property.
     *
     * @var string
     */
    protected $repositoryClass = YourRepository::class;

    /**
     * Firstname
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("Text")
     * @Extbase\Validate("StringLength", options={"minimum": 2, "maximum": 40})
     */
    protected $firstname = '';

}