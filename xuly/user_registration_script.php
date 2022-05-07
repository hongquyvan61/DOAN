<?php
    require '../connectdb/connect.php';
    require '../model/encrypt.php';
    session_start();
    /*$name= mysqli_real_escape_string($con,$_POST['name']);*/
    $con = ketnoi();
    $email=mysqli_real_escape_string($con,$_POST['email']);
    $regex_email="/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[_a-z0-9-]+)*(\.[a-z]{2,3})$/";
    if(!preg_match($regex_email,$email)){
        echo "Email không hợp lệ. Bạn sẽ được đưa về trang đăng kí...";
        ?>
        <meta http-equiv="refresh" content="2;url=../giaodien/signup.php" />
        <?php
    }
    $password=mysqli_real_escape_string($con,$_POST['password']);
    if(strlen($password)<6){
        echo "Password phải có ít nhất 6 kí tự. Bạn sẽ được đưa về trang đăng kí...";
        ?>
        <meta http-equiv="refresh" content="2;url=../giaodien/signup.php" />
        <?php
    }
    $contact=$_POST['contact'];
    $encryptmodel = new encrypt();
    $truoc = explode("@", $email);
    $mahoatruoc = $encryptmodel->apphin_mahoa($truoc[0]);
    $encrypttruoc = $mahoatruoc."@".$truoc[1];
    /*$city=mysqli_real_escape_string($con,$_POST['city']);
    $address=mysqli_real_escape_string($con,$_POST['address']);*/
    $duplicate_user_query="select user_id from user where email='$encrypttruoc'";
    $duplicate_user_result=mysqli_query($con,$duplicate_user_query) or die(mysqli_error($con));
    $rows_fetched=mysqli_num_rows($duplicate_user_result);
    if($rows_fetched>0){
        //duplicate registration
        //header('location: signup.php');
        ?>
        <script>
            window.alert("Email này đã tồn tại!");
        </script>
        <meta http-equiv="refresh" content="1;url=../giaodien/signup.php" />
        <?php
    }else{?>
        
        <?php
        $def = 1;
        $tiento = explode("@", $email);
        $mahoatiento = $encryptmodel->apphin_mahoa($tiento[0]);
        $encryptemail = $mahoatiento."@".$tiento[1];
        $encryptpass = $encryptmodel->apphin_mahoa($password);
//        $tam = $encryptmodel->mahoathongke(5432000000);
//        var_dump($tam);
//        var_dump($encryptmodel->giaimathongke($tam));
        $encryptsdt = $encryptmodel->apphin_mahoa($contact);
        $user_registration_query="insert into user(pass,email,sdt,role) values ('$encryptpass','$encryptemail','$encryptsdt','guest')";
//        //die($user_registration_query);
        $abc = 1;
        $user_registration_result=mysqli_query($con,$user_registration_query) or die(mysqli_error($con));
        
        
        //$_SESSION['email']=$email;
        //The mysqli_insert_id() function returns the id (generated with AUTO_INCREMENT) used in the last query.
        //$_SESSION['id']=mysqli_insert_id($con); 
        
        //header('location:http://localhost:8000/DOAN/giaodien/login.php');  //for redirecting
        ?>
        <script>
            window.alert("Đăng kí thành công!");
        </script>
        <meta http-equiv="refresh" content="3;url=../giaodien/login.php" />
        
        <?php
    }
    
?>