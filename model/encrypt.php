<?php
    class encrypt{
        private $alphabet = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
        
        //private $pvkey;
        public function vn_to_str ($str){
 
            $unicode = array(

            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',

            'd'=>'đ',

            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',

            'i'=>'í|ì|ỉ|ĩ|ị',

            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',

            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',

            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',

            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',

            'D'=>'Đ',

            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',

            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',

            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',

            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',

            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',

            );
 
            foreach($unicode as $nonUnicode=>$uni){

            $str = preg_replace("/($uni)/i", $nonUnicode, $str);

            }
            //$str = str_replace(' ','_',$str);

            return $str;
 
        }
        private function modinverse($key){
            for($i=0; $i<26; $i++){
		$flag= ($i*$key)%26;
		if($flag == 1){
			return $i;
		}
            }
            return -1;
        }
        private function modinverse2($key, $mod){
            for($i=0; $i<$mod; $i++){
		$flag= ($i*$key)%$mod;
		if($flag == 1){
			return $i;
		}
            }
            return -1;
        }
        private function modexp($a, $x, $n){
            $r=1;
            while ($x>0){
                    if ($x%2==1) 
                    {
                        $r=($r*$a)%$n;
                    }
                    $a=($a*$a)%$n;
                    $x/=2;
            }
            return $r;
        }
        private function USCLN($a, $b) {
            if ($b == 0) return $a;
            return $this->USCLN($b, $a % $b);
        }
        private function BSCNN($a, $b) {
            return ($a * $b) / $this->USCLN($a, $b);
        }
        public function mahoathongke($so){
            $arr = str_split($so);
            $encryptnum = "";
            $sokhackhong = "";
            $flag = 0;
            foreach($arr as $pt){
                if($pt*1 != 0){
                    $sokhackhong.=$pt;
                    $flag = 0;
                }
                else{
                    if(!empty($sokhackhong)){
                        $encryptnum.= $this->rsa_mahoa($sokhackhong);
                        $sokhackhong = "";
                    }
                    $encryptnum.="X";
                    $flag = 1;
                }
            }
            if($flag == 0){
                $encryptnum.= $this->rsa_mahoa($sokhackhong);
            }
            return $encryptnum;
        }
        
        public function giaimathongke($so){
            $arr = str_split($so);
            $decryptnum = "";
            $sokhackhong = "";
            $flag = 0;
            foreach($arr as $pt){
                $ordchar = ord($pt);
                if($ordchar != 35 && $ordchar != 88){
                    $sokhackhong.=$pt;
                }
                elseif ($ordchar == 35) {
                    $sokhackhong.=$pt;
                    $decryptnum.= $this->rsa_giaima($sokhackhong, 1015661);
                    $sokhackhong = "";
                }
                elseif($ordchar == 88){
                    $decryptnum.="0";
                }
            }
            return $decryptnum;
        }
        private function rsa_mahoa($m){
            /*SINH KHOA*/
//            $p = 661;
//            $q = 673;
            $p = 5039;
            $q = 673;
            $n = $p*$q;
            $phi_n = $this->BSCNN($p-1, $q-1);
            $e = -1;
            for($i = 2;$i < $phi_n;$i++){
                if($this->USCLN($i,$phi_n) == 1){
                    $e = $i;
                    break;
                }
            }
            //$this->pvkey = $this->modinverse2($e, $phi_n);
            /*END SINH KHOA*/
            /*MA HOA*/
            $arr = str_split($m);
            $dem = 0;
            $temp = "";
            $final = "";
            foreach($arr as $pt){
                if($dem < 5){
                    $temp.=$pt;
                    $dem++;
                }
                else{
                    $final.=$temp."#";
                    $temp = "";     
                    $temp.=$pt;
                    $dem = 1;
                }
            }
            if($dem <= 5){
                $final.=$temp."#";
                $array = str_split($final);
                $ketqua = "";
                $kqcuoicung = "";
                foreach($array as $pt){
                    $ordchar = ord($pt);
                    if($ordchar == 35){
                        $kqcuoi = $this->modexp($ketqua,$e,$n);
                        $kqcuoicung.=$kqcuoi."#";
                        $ketqua = "";
                    }
                    else{
                        $ketqua.=$pt;
                    }
                }
                return $kqcuoicung;
            }
            return "";
            //}
            
            /*END MA HOA*/
        }
        
        private function rsa_giaima($so, $privatekey){
            $arraykhac = str_split($so);
            $chuoi = "";
            $chuoi2 = "";
            foreach($arraykhac as $pt){
                    $ordchar = ord($pt);
                    if($ordchar == 35){
                        $str = $this->modexp($chuoi,$privatekey,5039*673);
                        $chuoi2.=$str;
                        $chuoi = "";
                    }
                    else{
                        $chuoi.=$pt;
                    }
            }
            return $chuoi2;
            //return $this->modexp($so, $privatekey, 5039*673);
        }
        public function apphin_mahoa($banro){
            $mahoatext = "";
            $arr = str_split($banro);
            $flagso = 0;
            $chuoiso = "";
            foreach ($arr as $char) {
                if(ctype_upper($char)){
                    if(is_numeric($char)){
                        $flagso = 1;
                        $chuoiso.=$char;
                    }
                    else{
                        if($flagso == 1){
                            $mahoatext.= $this->rsa_mahoa($chuoiso);
                            $chuoiso = "";
                            $flagso = 0;
                        }
                        $ordchar = ord($char);
                        if($ordchar != 32){
                            $beforemod = 5*($ordchar-65) + 6;
                            $aftermod = $beforemod % 26;
                            $aftermodint = (int)$aftermod;
                            $mahoatext.= strtoupper($this->alphabet[$aftermodint]);
                        }
                        else{
                            $mahoatext.=" ";
                        }
                    }
                }
                else{
                    if(is_numeric($char)){
                        $flagso = 1;
                        $chuoiso.=$char;
                    }
                    else{
                        if($flagso == 1){
                            $mahoatext.= $this->rsa_mahoa($chuoiso);
                            $chuoiso = "";
                            $flagso = 0;
                        }
                        $ordchar = ord($char);
                        if($ordchar != 32){
                            $beforemod = 5*($ordchar-97) + 6;
                            $aftermod = $beforemod % 26;
                            $aftermodint = (int)$aftermod;
                            $mahoatext.= $this->alphabet[$aftermodint];
                        }else{
                            $mahoatext.=" ";
                        }
                    }
                }
            }
            if($flagso == 1){
                $mahoatext.=$this->rsa_mahoa($chuoiso);
            }
            return $mahoatext;
        }
        
        public function apphin_giaima($banma){
            $nghichdao = $this->modinverse(5);
            $plaintext="";
            $arr = str_split($banma);
            $flagso = 0;
            $chuoiso = "";
            foreach ($arr as $char) {
                if(ctype_upper($char)){
                    if(is_numeric($char)){
                        $flagso = 1;
                        $chuoiso.=$char;
                    }
                    else{
                        if($flagso == 1){
                            $plaintext.= $this->rsa_giaima($chuoiso, 1015661);
                            $chuoiso = "";
                            $flagso = 0;
                        }
                        if(ord($char) != 32){
                             $c = ((((( ord($char)-65 ) - 6) + 26 ) % 26 )*$nghichdao) % 26;
                            $convint = (int)$c;
                            $plaintext.= strtoupper($this->alphabet[$convint]);
                        }
                        else{
                            $plaintext.=" ";
                        }   
                    }
                }
                else{
                    if(is_numeric($char)){
                        $flagso = 1;
                        $chuoiso.=$char;
                    }
                    else{
                        if($flagso == 1){
                            $plaintext.= $this->rsa_giaima($chuoiso."#", 1015661);
                            //echo $chuoiso."\n";
                            $chuoiso = "";
                            $flagso = 0;
                        }
                        if(ord($char) != 35){
                            if(ord($char) != 32){
                                $d = ((((( ord($char)-97 ) - 6) + 26 ) % 26 )*$nghichdao) % 26;
                                $convint = (int)$d;
                                $plaintext.= $this->alphabet[$convint];
                            }
                            else{
                                $plaintext.=" ";
                            }
                        }
                    }
                }
            }
            if($flagso == 1){
                $plaintext.=$this->rsa_giaima($chuoiso, 1015661);
            }
            return $plaintext;
        }
         public function diachi_mahoa($banro){
            $mahoatext = "";
            $arr = str_split($banro);
           foreach ($arr as $char) {   
                if(is_numeric($char)){
                     if($char+3>9)
                                $mahoatext.=$char+3-10;
                           else
                                    $mahoatext.=$char+3;
                               
                   
                }
               else{
               $ordchar=ord($char);
             if(ctype_upper($char)){                                                 
                 if(($ordchar>31 && $ordchar< 48) || ($ordchar>57 && $ordchar< 65) || ($ordchar>90 && $ordchar< 97)|| ($ordchar>122 && $ordchar< 127))
                        {
                             $mahoatext.=$char;
                        }
                   else{
                                  $beforemod = 5*($ordchar-65) + 6;
                        $aftermod = $beforemod % 26;
                        $aftermodint = (int)$aftermod;
                        $mahoatext.= strtoupper($this->alphabet[$aftermodint]);
                          
                   }
                   }
                
                else
                {           
                        if(($ordchar>31 && $ordchar< 48) || ($ordchar>57 && $ordchar< 65) || ($ordchar>90 && $ordchar< 97)|| ($ordchar>122 && $ordchar< 127))
                        {
                             $mahoatext.=$char;
                        }
                   else{
                                $beforemod = 5*($ordchar-97) + 6;
                                $aftermod = $beforemod % 26;
                                $aftermodint = (int)$aftermod;
                                $mahoatext.= $this->alphabet[$aftermodint];
                          
                   }
//                        switch ($ordchar) {
//                            case 32:
//                                $mahoatext.=" ";
//                                break;
//                             case 45:
//                                $mahoatext.="-";
//                                break;
//                             case 47:
//                                $mahoatext.="/";
//                                break;
//                             case 44:
//                                $mahoatext.=",";
//                                break;
//
//                            default :
//                                $beforemod = 5*($ordchar-97) + 6;
//                                $aftermod = $beforemod % 26;
//                                $aftermodint = (int)$aftermod;
//                                $mahoatext.= $this->alphabet[$aftermodint];
//                                break;
//                            }
                    }
           
               }
                }
        
            return $mahoatext;
        }
        public function diachi_giaima($banma){
             $nghichdao = $this->modinverse(5);
            $plaintext="";
            $arr = str_split($banma);
            foreach ($arr as $char) {
                                   
                       if(is_numeric($char)){
                           if($char-3<0)
                                $plaintext.=$char-3+10;
                           else
                                  $plaintext.=$char-3;
                             }     
                        else{
                             $ordchar=ord($char);
                            if(ctype_upper($char)){ 
                                
         if(($ordchar>31 && $ordchar< 48) || ($ordchar>57 && $ordchar< 65) || ($ordchar>90 && $ordchar< 97)|| ($ordchar>122 && $ordchar< 127))
                        {
                             $plaintext.=$char;
                        }
                   else{
                                $c = ((((( ord($char)-65 ) - 6) + 26 ) % 26 )*$nghichdao) % 26;
                            $convint = (int)$c;
                            $plaintext.= strtoupper($this->alphabet[$convint]);
                          
                   }
//                        switch (ord($char)) {
//                        case 32:
//                            $plaintext.=" ";
//                            break;
//                         case 45:
//                            $plaintext.="-";
//                            break;
//                         case 47:
//                            $plaintext.="/";
//                            break;
//                         case 44:
//                            $plaintext.=",";
//                            break;
//
//                        default:
//                           $c = ((((( ord($char)-65 ) - 6) + 26 ) % 26 )*$nghichdao) % 26;
//                            $convint = (int)$c;
//                            $plaintext.= strtoupper($this->alphabet[$convint]);
//                            break;
//                        }                  
                }
                else{
          if(($ordchar>31 && $ordchar< 48) || ($ordchar>57 && $ordchar< 65) || ($ordchar>90 && $ordchar< 97)|| ($ordchar>122 && $ordchar< 127))
                        {
                             $plaintext.=$char;
                        }
                   else{
                           $d = ((((( ord($char)-97 ) - 6) + 26 ) % 26 )*$nghichdao) % 26;
                            $convint = (int)$d;
                             $plaintext.= $this->alphabet[$convint];
                          
                   }
//                     switch (ord($char)) {
//                        case 32:
//                            $plaintext.=" ";
//                            break;
//                         case 45:
//                            $plaintext.="-";
//                            break;
//                         case 47:
//                            $plaintext.="/";
//                            break;
//                         case 44:
//                            $plaintext.=",";
//                            break;
//
//                        default:
//                            $d = ((((( ord($char)-97 ) - 6) + 26 ) % 26 )*$nghichdao) % 26;
//                            $convint = (int)$d;
//                             $plaintext.= $this->alphabet[$convint];
//                            break;
//                        }           
                }
            }
            }

            return $plaintext;
        }
    
    }
?>

