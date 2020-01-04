<?php


namespace BasicTablePackage;


use BasicTablePackage\Entity\Repository;
use BasicTablePackage\View\FormView\FormViewFieldConfiguration;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validation;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig_Environment;

define('DEFAULT_FORM_THEME', 'form_div_layout.html.twig');
define('VENDOR_DIR', realpath(__DIR__ . '/../../../concrete/vendor/'));
define('VENDOR_FORM_DIR', VENDOR_DIR . '/symfony/form');
define('VENDOR_VALIDATOR_DIR', VENDOR_DIR . '/symfony/validator');
define('VENDOR_TWIG_BRIDGE_DIR', VENDOR_DIR . '/symfony/twig-bridge');
define('VIEWS_DIR', realpath(__DIR__ . '/../resources'));

class FormViewService
{
    /**
     * @var FormViewFieldConfiguration
     */
    private $formViewFieldConfiguration;
    /**
     * @var Repository
     */
    private $repository;
    private $formRenderer;
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;
    private $twig;

    public function __construct(FormViewFieldConfiguration $formViewFieldConfiguration, Repository $repository)
    {
        $this->formViewFieldConfiguration = $formViewFieldConfiguration;
        $this->repository = $repository;

        $validator = Validation::createValidator();
// Set up the Translation component
        $translator = new Translator('en');
        $translator->addLoader('xlf', new XliffFileLoader());
        $translator->addResource('xlf',
            VENDOR_FORM_DIR . '/Resources/translations/validators.en.xlf',
            'en',
            'validators');
        $translator->addResource('xlf',
            VENDOR_VALIDATOR_DIR . '/Resources/translations/validators.en.xlf',
            'en',
            'validators');

        $twig = new Environment(new FilesystemLoader([
            VIEWS_DIR,
            VENDOR_TWIG_BRIDGE_DIR . '/Resources/views/Form',
        ]));
        $formEngine = new TwigRendererEngine(array(DEFAULT_FORM_THEME), $twig);
        $twig->addRuntimeLoader(new FactoryRuntimeLoader([
            FormRenderer::class => function () use ($formEngine) {
                return new FormRenderer($formEngine);
            },
        ]));
        $twig->addExtension(new TranslationExtension($translator));
        $this->twig = $twig;
        $this->twig->addExtension(
            new FormExtension()
        );

        $this->formFactory = Forms::createFormFactoryBuilder()
                                  ->addExtension(new ValidatorExtension($validator))
                                  ->getFormFactory();
    }

    /**
     * @return Twig_Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }


    public function getFormView($editId = null): \Symfony\Component\Form\FormBuilderInterface
    {
        if ($editId != null) {
            $entity = $this->repository->getById($editId);
        } else {
            $entity = $this->repository->create();
        }
        return $this->formFactory->createBuilder(FormType::class, $entity)
                                 ->add("intcolumn")
                                 ->add('save', SubmitType::class);
    }
}