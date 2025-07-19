<?php
$cartid=$_GET["cartid"];
include "../shared/connection.php";
$status=mysqli_query($conn,"delete from cart where cartid=$cartid");
if($status)
{
header("location:viewcart.php");
}

?>