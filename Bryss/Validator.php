<?php

namespace Bryss;

class Validator
{
    static $errors = true;
    public $json_arrays = [];

	static function check($arr, $data) {
		
		foreach ($arr as $value) {	
			if (empty($data[$value])) {
				return self::throwError('Data is missing', 900);
			}
		}
    }
    
    static function required($val){
        if($val==NULL || $val==""){
			return self::throwError('Required Value', 901);
        }
		return $val;
    }

    static function max($val, $length){
        $val_len = strlen($val);
        if($val_len>$length){
			return self::throwError("Maximum length of value reached. Value length must be less than $length", 901);
        }
		return $val;
    }

    static function min($val, $length){
        $val_len = strlen($val);
        if($val_len<$length){
			return self::throwError("Minimum length of value reached. Value length must be greater than $length", 901);
        }
		return $val;
    }

    static function len($val, $length){
        $val_len = strlen($val);
        if($val_len!=$length){
			return self::throwError("Value length must be equal to $length", 901);
        }
		return $val;
    }

	static function int($val) {
		$val = filter_var($val, FILTER_VALIDATE_INT);
		if ($val === false) {
			return self::throwError('Invalid Integer', 901);
		}
		return $val;
	}

	static function str($val) {
		if (!is_string($val)) {
			return self::throwError('Invalid String', 902);
		}
		$val = trim(htmlspecialchars($val));
		return $val;
	}

	static function bool($val) {
		$val = filter_var($val, FILTER_VALIDATE_BOOLEAN);
		return $val;
	}

	static function email($val) {
        $val = filter_var($val, FILTER_VALIDATE_EMAIL);
		if ($val === false) {
			return self::throwError('Invalid Email', 903);
		}
		return $val;
	}

	static function url($val) {
		$val = filter_var($val, FILTER_VALIDATE_URL);
		if ($val === false) {
			return self::throwError('Invalid URL', 904);
		}
		return $val;
    }
    
    static function schema($data, $schema){
        $checks = array(
            "required" =>"self::required",
            "email" =>"self::email",
            "min"=>"self::min",
            "max"=>"self::max",
            "str"=>"self::str",
            "int"=>"self::int",
            "bool"=>"self::bool",
            "url"=>"self::url",
            "len"=>"self::len",
        );

        $errors=array();
        foreach($schema as $sk => $sv){
            $sv_match_split = explode("|", $sv);
            if(array_key_exists($sk, $data)){
                $sk_data = $data[$sk];
                
                foreach($sv_match_split as $sk_check){
                    $_sk_check_values = explode(":", $sk_check);
                    if(count($_sk_check_values)==1){
                        $validate = call_user_func_array($checks[$_sk_check_values[0]], [$sk_data]);
                    }else{
                        $validate = call_user_func_array($checks[$_sk_check_values[0]], [$sk_data, $_sk_check_values[1]]);
                    }
                    if(is_array($validate) && $validate["valid"]==false){
                        array_push($errors, array(
                            "field"=>$sk,
                            "message"=>$validate["data"]["message"]
                        ));
                    }
                }
            }else{
               if(in_array("required", $sv_match_split))
               {
                array_push($errors, array(
                    "field"=>$sk,
                    "message"=>"Field is required. Data is missing."
                ));
               } 
                
            }
        }
        return $errors;
    }

	static function throwError($error = 'Error In Processing', $errorCode = 0) {
		if (self::$errors === true) {
            return array(
                "valid"=>false,
                "data"=>array(
                "message"=>$error,
                "code"=>$errorCode
            ));
           
		}
    }
    
}
