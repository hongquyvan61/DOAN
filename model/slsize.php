<?php
    
            require '../connectdb/connect.php';
            $con = ketnoi();
            $size = $_POST['size'];
            $shoe = (int)$_POST['shoe'];
            $qty = 0;
            $query = "select qty$size from shoes where shoe_id=$shoe";
            $result = mysqli_query($con, $query);
            if(mysqli_num_rows($result)!=0){
                while($row = mysqli_fetch_assoc($result)){
                        $qty = (int)$row["qty$size"];
                        break;
                }
            }
         
            echo $qty;
            //$query = "insert into(name,brand,size,category,price,quantity_left,image)
        //values (ajsad,adidas,49,kjdkas,1000000,5,img/".tenhang."/".tenhinh.");"
?>
