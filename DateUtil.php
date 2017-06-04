<?php

class DateUtil
{
    /********* 日付関連 *********/
    /**
     * 指定した日付が含まれる週の月曜日の日付を取得する
     *
     * 週は月曜始まり日曜終わり
     *
     * @param string $date YYYYMMDD等の日付文字列　例：'20170601'
     * @param string $format 戻り値の日付フォーマット
     * @return string 月曜日の日付
     */
    public static function getMondayDate($date, $format = 'Ymd') {
        // strtotime()は週が日曜始まりで扱われるため、strtotime($date . ' this week monday')だと$dateが日曜の場合に1週ずれる
        return date($format, strtotime('last monday', strtotime($date . ' +1 day')));
    }

    /**
     * 日付文字列のフォーマットを変更する
     * 
     * 'Y年m月d日(l)', 'Y年m月d日(l)'のように$formatを指定することで曜日を日本語で出力できる
     *
     * @param string $date YYYYMMDD等の日付文字列 例：'20170601'
     * @param string $format フォーマット文字列
     * @return string フォーマット済日付文字列
     */
    public static function getDateLabel($date, $format = 'Y/m/d') {
        $week_days = array('日', '月', '火', '水', '木', '金', '土');
        $day_of_the_week = $week_days[date('w', strtotime($date))];
        $format = preg_replace(array('/D/', '/l/'), array($day_of_the_week, $day_of_the_week . '曜日'), $format);

        return date($format, strtotime($date));
    }


    /********** 日付計算関連 ************/
    /**
     * 指定ヶ月前の日付を返す
     *
     * (例) 3/31 の一ヶ月前を、2/28として返す。
     * （php標準関数などで、一ヶ月前を求める {strtotime('-1 month', strtotime('2009-03-31'));} と、3/3 が返る。）
     *
     * @param mixed $date 指定ヵ月後の日付を求める基準となる日付（入力可能な形式：Y-m-d, Y/m/d, Ymd, タイムスタンプ, DateTimeオブジェクト）
     * @param integer $any_month_later 指定月数
     * @return mixed 指定ヵ月前の日付（出力形式は入力形式に応じて形式を維持）
     */
    public static function getDateOfAnyMonthAgo($date, $any_month_ago) {
        return self::_wrapperCalculateDatetime($date, -$any_month_ago, 'self::_getAnyMonthLater');
    }
    
    /**
     * 指定ヵ月後の日付を返す
     *
     * (例)1/31 の一ヶ月後を、2/28として返す。
     * （php標準関数などで、一ヶ月後を求める {strtotime('+1 month', strtotime('2009-01-31'));} と、3/3 が返る。）
     *
     * @param mixed $date 指定ヵ月後の日付を求める基準となる日付（入力可能な形式：Y-m-d, Y/m/d, Ymd, タイムスタンプ, DateTimeオブジェクト）
     * @param integer $any_month_later 指定月数
     * @return mixed 指定ヵ月後の日付（出力形式は入力形式に応じて形式を維持）
     */
    public static function getDateOfAnyMonthLater($date, $any_month_ago) {
        return self::_wrapperCalculateDatetime($date, $any_month_ago, 'self::_getAnyMonthLater');
    }

    /**
     * 指定ヵ月後のタイムスタンプを返す
     *
     * (例)1/31 の一ヶ月後を、2/28として返す。
     * （php標準関数などで、一ヶ月後を求める {strtotime('+1 month', strtotime('2009-01-31'));} と、3/3 が返る。）
     * (例) 3/31 の一ヶ月前を、2/28として返す。
     * （php標準関数などで、一ヶ月前を求める {strtotime('-1 month', strtotime('2009-03-31'));} と、3/3 が返る。）
     *
     * @param integer $timestamp 指定ヵ月後の日付を求める基準となる日付
     * @param integer $any_month_later 指定月数。負の数の場合は指定ヶ月前
     * @return integer 指定ヵ月後の日付タイムスタンプ
     */
    private static function _getAnyMonthLater($timestamp, $any_month_later) {
        // 指定ヵ月後の日付
        $a_date_of_any_month_later = strtotime($any_month_later . ' month', $timestamp);

        // 指定ヵ月後の日付の、その月の最終日付
        $last_date_of_any_month_later = strtotime('last day of ' . $any_month_later . ' month', $timestamp);

        // 指定ヶ月後の日付が最終日付を超える場合は最終日付を返す
        return min($a_date_of_any_month_later, $last_date_of_any_month_later);
    }

    /**
     * 指定ヵ月後の初日の日付を返す
     *
     * (例)2013/1/31 の一ヶ月後を、2013/2/1として返す。
     *     2013/1/31 の一ヶ月前を、2012/12/1として返す。
     * @param mixed $date 指定ヵ月後の日付を求める基準となる日付（入力可能な形式：Y-m-d, Y/m/d, Ymd, タイムスタンプ, DateTimeオブジェクト）
     * @param integer $any_month_later 指定月数
     * @return mixed 指定ヵ月後の日付（出力形式は入力形式に応じて形式を維持）
     */
    public static function getFirstDateOfAnyMonthLater($date, $any_month_later) {
        return self::_wrapperCalculateDatetime($date, $any_month_later, 'self::_getFirstDateOfAnyMonthLater');
    }

    /**
     * 指定ヵ月後の初日のタイムスタンプを返す
     *
     * @param integer $timestamp 基準となる日付
     * @param integer $any_month_later 指定月数。負の数の場合は指定ヶ月前
     * @return integer 指定ヵ月後の日付タイムスタンプ
     */
    private static function _getFirstDateOfAnyMonthLater($timestamp, $any_month_later) {
        // 指定ヵ月後の日付の、その月の最初の日付
        return strtotime('first day of ' . $any_month_later . ' month', $timestamp);
    }

    /**
     * 指定日の末日の日付を返す
     *
     * @param mixed $date 基準となる日付（入力可能な形式：Y-m-d, Y/m/d, Ymd, タイムスタンプ, DateTimeオブジェクト）
     * @return mixed 指定ヵ月後の日付（出力形式は入力形式に応じて形式を維持）
     */
    public static function getLastDateOfMonth($date) {
        return self::_wrapperCalculateDatetime($date, null, 'self::_getLastDateOfMonth');
    }

    /**
     * 指定日の末日のタイムスタンプを返す
     *
     * @param integer $timestamp 基準となる日付
     * @param integer $any_month_later 指定月数。負の数の場合は指定ヶ月前
     * @return integer 指定ヵ月後の日付タイムスタンプ
     */
    private static function _getLastDateOfMonth($timestamp, $any_month_later) {
        // 指定ヵ月後の日付の、その月の最初の日付
        return strtotime('last day of this month', $timestamp);
    }

    /*
     * 日付時間計算関数のラッパー
     *
     * @param mixed $date 指定ヵ月後の日付を求める基準となる日付（入力可能な形式：Y-m-d, Y/m/d, Ymd, タイムスタンプ, DateTimeオブジェクト）
     * @param integer $any_month_later 指定月数
     * @param mixed $func 時間変換関数 (タイムスタンプ, $any_month_later => タイムスタンプ)
     * @return mixed 指定ヵ月後の日付（出力形式は入力形式に応じて形式を維持）
     */
    private static function _wrapperCalculateDatetime($date, $any_month_later, $func) {
        $output_filter = null;
        $timestamp = $date;

        if (is_string($date)) {
            // 入力形式が文字列
            $matches = array();
            if (!preg_match('/^([1-9][0-9]{3})(?<delimiter1>.?)([0-9]{2})(?<delimiter2>.?)([0-9]{2})$/uD', $date, $matches)) {
                throw new Exception(array('基準日付' => $date));
            }
            $output_filter = function ($timestamp) use ($matches) {
                return date('Y' . $matches['delimiter1'] . 'm' . $matches['delimiter2'] . 'd', $timestamp);
            };

            $timestamp = strtotime($date);
        } else if (is_int($date)) {
            // 入力形式がタイムスタンプ
            if ($date < 0) {
                throw new Exception(array('基準日付' => $date));
            }

            $output_filter = function ($timestamp) {
                return $timestamp;
            };

            $timestamp = $date;
        } else if ($date instanceof DateTime) {
            // 入力形式がDateTimeオブジェクト
            $output_filter = function ($timestamp) {
                $datetime = new DateTime();
                $datetime->setTimestamp($timestamp);
                return $datetime;
            };

            $timestamp = $date->getTimestamp();
        } else {
            throw new Exception(array('基準日付' => $date));
        }

        return $output_filter(call_user_func_array($func, array($timestamp, $any_month_later)));
    }

    /********* 時間関連 *********/
    /**
     * 時間文字列をフォーマットする
     *
     * @param string $time フォーマット前時間文字列　例：'1730'
     * @return string フォーマット済時間文字列
     */
    public static function getTimeLabel($time) {
        if (empty($time)) {
            return $time;
        }else{
            $matches = array();
            if (!preg_match('/^(\d{2})(\d{2})$/uD', $time, $matches)) {
                throw new Exception('フォーマット前時間文字列が不正です');
            }
        }
        return $matches[1] . ':' . $matches[2];
    }

    /**
     * 2つの時間文字列(start, end)の差 end-start を取る
     * 例) 1330, 1700 -> 0330
     *
     * @param string $start_time, $end_time フォーマット前時間文字列
     * @return string 時間文字列 引数がnullの場合はnull
     */
    public static function getDifferenceBetweenTimeLabel($start_time, $end_time) {
        if (empty($start_time) || empty($end_time)) {
            return null;
        }else{
            $start_hm = array();
            if (!preg_match('/^(\d{2})(\d{2})$/uD', $start_time, $start_hm)) {
                throw new Exception('フォーマット前時間文字列が不正です');
            }
            $end_hm = array();
            if (!preg_match('/^(\d{2})(\d{2})$/uD', $end_time, $end_hm)) {
                throw new Exception('フォーマット前時間文字列が不正です');
            }

            // 時間を分に計算
            $start_min = $start_hm[1] * 60 + $start_hm[2];
            $end_min = $end_hm[1] * 60 + $end_hm[2];
            // 分の差を取る endの方が小さければ翌日扱い
            $difference_min = ($end_min >= $start_min)? $end_min - $start_min : $end_min + 1440 - $start_min;
            // 分を時間文字列にフォーマット
            $difference_h = intval($difference_min / 60);
            $difference_m = $difference_min % 60;
            $difference_h_str = str_pad($difference_h, 2, "0", STR_PAD_LEFT);
            $difference_m_str = str_pad($difference_m, 2, "0", STR_PAD_LEFT);

            return $difference_h_str . $difference_m_str;
        }
        return null;
    }

    /**
     * 時間文字列startと、時間差diffの和を取る
     * 例) 1330, 0330 -> 1700
     *
     * @param string $start_time, $diff 時間文字列
     * @return string 時間文字列 引数がnullの場合はnull
     */
    public static function patchDiffOfStartTimeLabel($start_time, $diff) {
        if (empty($start_time) || empty($diff)) {
            return null;
        }else{
            $start_hm = array();
            if (!preg_match('/^(\d{2})(\d{2})$/uD', $start_time, $start_hm)) {
                throw new Exception('フォーマット前時間文字列が不正です');
            }
            $diff_hm = array();
            if (!preg_match('/^(\d{2})(\d{2})$/uD', $diff, $diff_hm)) {
                throw new Exception('フォーマット前時間文字列が不正です');
            }

            // 時間を分に計算
            $start_min = $start_hm[1] * 60 + $start_hm[2];
            $diff_min = $diff_hm[1] * 60 + $diff_hm[2];
            // 分の和を取る 1日より大きければ翌日扱い
            $end_min = $start_min + $diff_min;
            if ($end_min >= 1440){
                $end_min -= 1440;
            }
            // 分を時間文字列にフォーマット
            $end_h = intval($end_min / 60);
            $end_m = $end_min % 60;
            $end_h_str = str_pad($end_h, 2, "0", STR_PAD_LEFT);
            $end_m_str = str_pad($end_m, 2, "0", STR_PAD_LEFT);

            return $end_h_str . $end_m_str;
        }
        return null;
    }


    /********** "期"関連 ************/
    /**
     * 日付をYYYY年上期(1)/下期(2)の形に変換する
     * 
     * 例：'20170601' → '20171'
     *
     * @param string $date YYYYMMDD等の日付文字列
     * @return string フォーマット済　年期文字列
     */
    public static function getDateTerm($date) {
        $fiscal_year_start_month = 4;
        $timestamp = strtotime($date);
        $month = date('m', $timestamp);

        // 年度：始まり月より前なら前年と扱う
        $year = ($month < $fiscal_year_start_month)? date('Y', $timestamp) - 1 : date('Y', $timestamp);
        // 年度の始まり月からの経過月数(0-11)
        $passed_month_count = ($month < $fiscal_year_start_month)? $month + 12 - $fiscal_year_start_month : $month - $fiscal_year_start_month;
        // 経過月数が6か月以下なら(0-5)上期、7か月以上なら(6-11)下期
        $term = ($passed_month_count < 6)? 1 : 2;

        return $year . $term;
    }

    /**
     * 対象半期の値を文字にする
     *
     * @param string/integer $yt 年+半期 (YYYY+1) 上期:1,下期:2
     * @return string ラベル
     */
    public static function makeYearHalfLabel($yt){
        $matches = array();
        if (!preg_match('/^(?<year>[1-2][0-9]{3})(?<half>[1-2])$/uD', $yt, $matches)) {
            throw new Exception('不正なフォーマットです。');
        }
        return $matches['year'] . '年' . (($matches['half']=='1')? '上期' : '下期');
    }


    /********** リスト作成系 ************/
    /**
     * 年のリストを作成する
     *
     * デフォルトではキーと値に年の数値がそのまま入ったリストを生成する。
     * 適用関数を指定することでちょっとした違いのリストであればこのメソッドで生成できる。
     * 例：キーに年の数値、値に「○○年」のように「年」を付加した値が入ったリストを生成する場合
     * <pre>
     *   DateUtil::makeYearList(2010, 2011, true, function($x) {
     *       return array($x[1], $x[1] . '年');
     *   });
     * </pre>
     *
     * @param integer $begin 開始年
     * @param integer $end 終了年
     * @param boolean $is_asc 昇順かどうか（デフォルト：true）
     * @param function $func 適用関数（デフォルトでは年がキーと値それぞれに入る）
     * @param integer $step 何年刻みか
     * @return array 年リスト
     */
    public static function makeYearList($begin, $end, $is_asc = true, $func = 'DateUtil::sameKeyValue', $step = 1) {
        return self::_makeSequence($begin, $end, $is_asc, $step, $func);
    }

    /**
     * 月のリストを作成する
     *
     * 4月始まり3月終わりのようなリストは作成できない。
     *
     * @param boolean $is_asc 昇順かどうか（デフォルト：true）
     * @param integer $begin 開始月
     * @param integer $end 終了月
     * @param function $func 適用関数（デフォルトでは0埋めの月がキーと値それぞれに入る）
     * @param integer $step 何月刻みか
     * @return array 月リスト
     */
    public static function makeMonthList($is_asc = true, $begin = 1, $end = 12, $func = null, $step = 1) {
        if (!$func) {
            $func = function($x) {
                $value = sprintf('%02d', $x[1]);
                return array($value, $value);
            };
        }
        return self::_makeSequence($begin, $end, $is_asc, $step, $func);
    }

    /**
     * 対象年月のリストを作成する
     *
     * @param string/integer $begin 開始年月 (YYYYmm)
     * @param string/integer $end  終了年月 (YYYYmm)
     * @param boolean $is_asc 昇順かどうか（デフォルト：true）
     * @param function $func 適用関数（デフォルトでは年月を文字にする）
     * @return array 年月リスト
     */
    public static function makeYearMonthList($begin, $end, $is_asc = true, $func = 'DateUtil::makeYearMonthLabel') {
        function nextIterate($i, $is_asc){
            if ($is_asc){
                return ($i % 100 == 12)? $i + 89 : $i + 1;
            } else {
                return ($i % 100 == 1)? $i - 89 : $i - 1;
            }
        }

        $result = array();
        $count = 0;
        if (!$is_asc){
            self::swap($begin, $end);
        }
        $end = nextIterate($end, $is_asc);

        for ($i = $begin; $i != $end; $i = nextIterate($i, $is_asc)) {
            $result[$i] = call_user_func($func, $i);
        }

        return $result;
    }

    /**
     * シーケンスを作成する
     *
     * 終了値は開始値から増加量を加算していって等しい値になること。
     * 適用関数は引数に array(0ベースインデックス, 値) を受け取って
     * array(インデックス, 値) を戻り値として返すような関数。
     *
     * @param integer $begin 開始値
     * @param integer $end 終了値
     * @param boolean $is_asc 昇順かどうか（デフォルト：true）
     * @param integer $step 増加量
     * @param function $func 適用関数
     * @return array シーケンス
     */
    private static function _makeSequence($begin, $end, $is_asc = true, $step = 1, $func = 'DateUtil::identity') {
        if ($is_asc) {
            $end += $step;
        } else {
            $step = -1 * $step;
            self::swap($begin, $end);
            $end += $step;
        }

        $result = array();
        $count = 0;
        for ($i = $begin; $i != $end; $i += $step, ++$count) {
            list($key, $value) = call_user_func($func, array($count, $i));
            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * 対象年月の値を文字にする
     * 主にDateUtil::makeYearMonthListで使うが、単体でも使う
     *
     * @param string/integer $ym 年月 (YYYYmm)
     * @return string 年月ラベル
     */
    public static function makeYearMonthLabel($ym){
        $matches = array();
        if (!preg_match('/^(?<year>[1-2][0-9]{3})(?<month>(0[1-9]|1[0-2]))$/uD', $ym, $matches)) {
            throw new Exception('不正なフォーマットです。');
        }
        return $matches['year'] . '年' . $matches['month'] . '月';
    }

    /*********** 汎用関数 **********/
    /**
     * Swap関数
     *
     * @param mixed $a 値A
     * @param mixed $b 値B
     */
    public static function swap(&$a, &$b) {
        $t = $b;
        $b = $a;
        $a = $t;
    }
    
    /**
     * Key-Value形式の配列をValue-Value形式に変換する
     *
     * @param array $array (キー, 値)の配列
     * @return array (値, 値)の配列
     */
    public static function sameKeyValue($pair) {
        $value = array_pop($pair);
        return array($value, $value);
    }
    
    /**
     * 恒等関数
     *
     * @param mixed $x 値
     * @return mixed $xをそのまま返す
     */
    public static function identity($x) {
        return $x;
    }
}
