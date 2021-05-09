<?php
	if($_POST){
		$method=$_POST['method'];
		$mainamount=$_POST['mainamount'];
		$adjstamount=$_POST['adjstamount'];

		if($method=='plus'){
			echo $mainamount+$adjstamount;
		}else{
			echo $mainamount-$adjstamount;
		}
	}
?>