<?php 

foreach($providers as $provider):
	?><a href="/HybridAuth/Login/?provider=<?php echo $provider ?>"><?php echo $provider ?></a><br><?php 
endforeach;	
?>