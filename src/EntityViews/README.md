# EntityViews
If you don't want the standard layout of the form, after every label and field a newline, you can define a FormView.
You can define your own EntityView. To do that, create a class which extends AbstractFormView in src/EntityViews
and implement the method getFormView($form, $clientSideValidationActivated=true).  
An Example is in [src/EntityViews/ExampleEntityFormView.php](src/EntityViews/ExampleEntityFormView.php).
Then in your Entity, override the method setDefaultFormViews. There you set $this->defaultFormView to new YourDefaultFormViewClassname($this); 
View if it is shown in a DirectEditAssociationEntityField, you set the $this->defaultSubFormView.