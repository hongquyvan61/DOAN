<?php
    session_start();
    require '../connectdb/connect.php';
    require '../model/encrypt.php';
    $con = ketnoi();
    if(!isset($_SESSION['email'])){
        header('location:index.php');
    }  
    $useremail= mysqli_real_escape_string($con,$_POST['useremail']);
    $usercontact= mysqli_real_escape_string($con,$_POST['usercontact']);
    $regex_email="/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[_a-z0-9-]+)*(\.[a-z]{2,3})$/";
    if(!preg_match($regex_email,$useremail)){
        ?>
        <script>window.alert("Email không hợp lệ!")</script>
        <meta http-equiv="refresh" content="2;url=../giaodien/suattcanhan.php" />
        <?php
    }
    else{
            $userid=$_SESSION['id'];
        $truoc = explode("@", $useremail);
        $mahoaemail = new encrypt();
        $encrypt = $mahoaemail->apphin_mahoa($truoc[0])."@".$truoc[1];
        $_SESSION['email'] = $useremail;
        $_SESSION['pvkeyemail'] = $mahoaemail->getrsakey();
        //echo $email;
        $query="update user set email='$encrypt', sdt='$usercontact' where user_id=$userid";
        $result=mysqli_query($con,$query) or die(mysqli_error($con));
        if(mysqli_affected_rows($con) > 0){
            ?>
            <script>
                window.alert("Change information successfully!");
            </script>
            <meta http-equiv="refresh" content="2;url=../giaodien/products.php" /> 
            <?php
        }
        else{
            ?>
            <script>
                window.alert("Fail to change information!");
            </script>
            <meta http-equiv="refresh" content="2;url=../giaodien/suattcanhan.php" />    
            <?php
        }
    }
    
?>