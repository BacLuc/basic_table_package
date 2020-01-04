<?php
/** @noinspection ALL */
$action = $this->action('post_form') . (isset($editId) && $editId != null ? "/$editId" : "");
/** @var \Symfony\Component\Form\FormBuilderInterface $formBuilder */
$form = $formBuilder->setAction($action)->getForm();
/** @var Twig_Environment $twig */
echo $twig->render('test.html.twig',
    [
        'form' => $form->createView()
    ]);

?>
