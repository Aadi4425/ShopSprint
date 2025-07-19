<?php
$price=$_GET["price"];
include "../shared/connection.php";
$status=mysqli_query($conn,"delete from product where price=$price");
if($status)
{
header("location:view.php");
}

?>