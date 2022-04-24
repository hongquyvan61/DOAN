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
                        $beforemod = 5*($ordchar-65) + 6;
                        $aftermod = $beforemod % 26;
                        $aftermodint = (int)$aftermod;
                        $mahoatext.= strtoupper($this->alphabet[$aftermodint]);
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
            $abc = 1;
            return $plaintext;
        }
    }
?>

