<?php
use App\Helpers\View;
View::addBeforeBody( 'addBeforeBody' );?>
<?php 
//Add Hook
View::addAfterBody( 'addAfterBody' );
View::addBeforeFooter( 'addBeforeFooter' );
View::addAfterFooter( 'addAfterFooter' ); 
View::addBeforeFooter( 'addBeforeFooter' );
View::addAfterFooter( 'addAfterFooter' ); 
?>

<?php View::renderHeader();?>

Content

<?php View::renderFooter(); ?>