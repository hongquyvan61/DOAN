<?php
    session_start();
    require '../connectdb/connect.php';
    require '../model/encrypt.php';
    $con = ketnoi();
    if(!isset($_SESSION['email'])){
        header('location:index.php');
    }  
    $old_password= mysqli_real_escape_string($con,$_POST['oldPassword']);
    $new_password= mysqli_real_escape_string($con,$_POST['newPassword']);
    
    $model = new encrypt();
    $truoc = explode("@", $_SESSION['email']);
    $encryptemail = $model->apphin_mahoa($truoc[0])."@".$truoc[1];
    /*$email=$_SESSION['email'];*/
    //echo $email;
    $password_from_database_query="select pass from user where email='$encryptemail'";
    $password_from_database_result=mysqli_query($con,$password_from_database_query) or die(mysqli_error($con));
    $row=mysqli_fetch_array($password_from_database_result);
    //echo $row['password'];
    
    $decryptoldpass = $model->apphin_giaima($row['pass']);
    if($decryptoldpass == $old_password){
        
        $encryptnewpass = $model->apphin_mahoa($new_password);
        
        $update_password_query="update user set pass='$encryptnewpass' where email='$encryptemail'";
        $update_password_result=mysqli_query($con,$update_password_query) or die(mysqli_error($con));
        echo "Your password has been updated.";
        ?>
        <meta http-equiv="refresh" content="3;url=../giaodien/products.php" />
        <?php
    }else{
        ?>
        <script>
            window.alert("Wrong password!!");
        </script>
        <meta http-equiv="refresh" content="1;url=../giaodien/settings.php" />
        <?php
        //header('location:settings.php');
    }
?>