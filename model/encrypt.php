<?php
    class encrypt{
        private $alphabet = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
       
        //private $pvkey;
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
            $c = "";
//            if(strlen($m) > 5){
//                $arr = str_split($m);
//                $dem = 1;
//                $tempnum = "";
//                foreach ($arr as $num) {
//                    if($dem <= 5){
//                        $tempnum.=$num;
//                        $dem++;
//                    }
//                    else{
//                        $c.= $this->modexp($tempnum, $e, $n);
//                        $dem = 1;
//                        $tempnum = "";
//                        $tempnum.=$num;
//                    }
//                }
//                if($dem <= 5){
//                    $c.=$this->modexp($tempnum, $e, $n);
//                }
//            }
//            else{
                $c = $this->modexp($m, $e, $n);
            //}
            
            /*END MA HOA*/
            return $c;
        }
        
        private function rsa_giaima($so, $privatekey){
//            $c = "";
//            if(strlen($so) > 5){
//                $arr = str_split($so);
//                $dem = 1;
//                $tempnum = "";
//                foreach ($arr as $num) {
//                    if($dem <= 5){
//                        $tempnum.=$num;
//                        $dem++;
//                    }
//                    else{
//                        $c.=$this->modexp($tempnum, $privatekey, 709*719);
//                        $dem = 1;
//                        $tempnum = "";
//                        $tempnum.=$num;
//                    }
//                }
//                if($dem <= 5){
//                    $c.=$this->modexp($tempnum, $privatekey, 709*719);
//                }
//            }
//            else{
//                $c = $this->modexp($so, $privatekey, 709*719);
//            }
//            return $c;
            return $this->modexp($so, $privatekey, 5039*673);
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
                        $beforemod = 5*($ordchar-65) + 6;
                        $aftermod = $beforemod % 26;
                        $aftermodint = (int)$aftermod;
                        $mahoatext.= $this->alphabet[$aftermodint];
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
                        $beforemod = 5*($ordchar-97) + 6;
                        $aftermod = $beforemod % 26;
                        $aftermodint = (int)$aftermod;
                        $mahoatext.= $this->alphabet[$aftermodint];
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
                        $c = (( ord($char)-65 ) - 6)*$nghichdao % 26;
                        $convint = (int)$c;
                        $plaintext.= $this->alphabet[$convint];
                    }
                }
                else{
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
                        $d = ((((( ord($char)-97 ) - 6) + 26 ) % 26 )*$nghichdao) % 26;
                        $convint = (int)$d;
                        $plaintext.= $this->alphabet[$convint];
                    }
                }
            }
            if($flagso == 1){
                $plaintext.=$this->rsa_giaima($chuoiso, 1015661);
            }
            return $plaintext;
        }
    }
?>

