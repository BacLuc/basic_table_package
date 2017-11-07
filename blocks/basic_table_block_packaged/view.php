<?php
//use Application\Block\BasicTableBlock\Controller as Controller;

defined('C5_EXECUTE') or die("Access Denied.");
// basically a stub that includes some other files
$u = new User();
$uID = $u->getUserID();
$c = Page::getCurrentPage();
//$controller = new Controller();


$canAccessComposer = false;
if (is_object($composer)) {
    $ccp = new Permissions($composer);
    if ($ccp->canAccessComposer()) {
        $canAccessComposer = true;
    }
}
if (!$controller->isExecuted()) {
    if ($controller->displayForm()) {
        include($controller->getBasicTablePath() . '/views/form_view.php');
    } else {
        include($controller->getBasicTablePath() . '/views/table_view.php');
        $controller->setExecuted();
    }
}


?>

<script type="text/javascript">
    /*
//test the ajax funcitonality
    $(document).ready(function(e){
        $.ajax({
              type: "POST",
              url: "<?php echo $this->action('myAction') ?>",
		      dataType: 'json',
		      data:{test: 'test'
			      },
		      success: function(j){

				  //in ie, when the debugging console is not open, console is not defined -> errror
				      if(console) {
						  console.log(j);
					  }
	
			      }
	
		});
	
});

*/
</script>



