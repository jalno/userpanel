<?php
namespace themes\clipone;
class utility{
	static function switchcase($item, array $items){
		foreach($items as $return => $cases){
			if(is_array($cases)){
				foreach($cases as $case){
					if($item == $case){
						return $return;
					}
				}
			}elseif($cases == $item){
				return $return;
			}
		}
		return false;
	}
	static function selectOptions($rows, $selected= null){
		$html = '';
		foreach($rows as $value => $title){
			if(is_array($title)){
				$values = array_column($rows, 'value');
				$titles = array_column($rows, 'title');
				return self::selectOptions(array_combine($values, $titles), $selected);
			}else{
				$html .= "<option value=\"{$value}\"".($selected == $value ? ' selected' : '').">{$title}</option>";
			}
		}
		return $html;
	}
	static function radiobox($rows, $selected= null){
		$html = '';
		foreach($rows as $value => $title){
			if(is_array($title)){
				$values = array_column($rows, 'value');
				$titles = array_column($rows, 'title');
				return self::radiobox(array_combine($values, $titles), $selected);
			}else{
				$html .= "<option value=\"{$value}\"".($selected == $value ? ' selected' : '').">{$title}</option>";
			}
		}
		return $html;
	}
	static function dateFormNow($time){
        $thisTime = time();
        $mine = $thisTime - $time;
        if($mine > 0){
            if($mine < 60){
                return("$mine ثانیه پیش");
            }elseif($mine < 3600){
                return(round($mine/60)." دقیقه پیش");
            }elseif($mine < 86400){
                return(round($mine / 3600)." ساعت پیش");
            }elseif($mine < 604800){
                return(round($mine / 86400)." روز پیش");
            }elseif($mine < 2592000){
                return(round($mine / 604800)." هفته پیش");
            }else{
                return(round($mine/2592000)." ماه پیش");
            }
        }else{
            $mine = $time - $thisTime;
            if($mine == 0){
            	return("همین الان");
            }elseif($mine < 60){
                return("$mine ثانیه بعد");
            }elseif($mine < 3600){
                return(round($mine/60)." دقیقه بعد");
            }elseif($mine < 86400){
                return(round($mine / 3600)." ساعت بعد");
            }elseif($mine < 604800){
                return(round($mine / 86400)." روز بعد");
            }elseif($mine < 2592000){
                return(round($mine / 604800)." هفته بعد");
            }else{
                return(round($mine/2592000)." ماه بعد");
            }
        }
    }
}
