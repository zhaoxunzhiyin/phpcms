//获取系统时间
var newDate = '';
getLangDate();
//值小于10时，在前面补0
function dateFilter(date){
    if(date < 10){return "0"+date;}
    return date;
}
function getLangDate(){
    var FIRSTYEAR = 1998;
    var LASTYEAR = 2031;
    var dateObj = new Date(); //表示当前系统时间的Date对象
    var year = dateObj.getFullYear(); //当前系统时间的完整年份值
    var month = dateObj.getMonth()+1; //当前系统时间的月份值
    var date = dateObj.getDate(); //当前系统时间的月份中的日
    var day = dateObj.getDay(); //当前系统时间中的星期值
    var weeks = ["星期日","星期一","星期二","星期三","星期四","星期五","星期六"];
    var week = weeks[day]; //根据星期值，从数组中获取对应的星期字符串
    var hour = dateObj.getHours(); //当前系统时间的小时值
    var minute = dateObj.getMinutes(); //当前系统时间的分钟值
    var second = dateObj.getSeconds(); //当前系统时间的秒钟值
    var LunarCal = [
    new tagLunarCal( 27,  5, 3, 43, 1, 0, 0, 1, 0, 0, 1, 1, 0, 1, 1, 0, 1 ),
    new tagLunarCal( 46,  0, 4, 48, 1, 0, 0, 1, 0, 0, 1, 0, 1, 1, 1, 0, 1 ), /* 88 */
    new tagLunarCal( 35,  0, 5, 53, 1, 1, 0, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1 ), /* 89 */
    new tagLunarCal( 23,  4, 0, 59, 1, 1, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1 ),
    new tagLunarCal( 42,  0, 1,  4, 1, 1, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1 ),
    new tagLunarCal( 31,  0, 2,  9, 1, 1, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 0 ),
    new tagLunarCal( 21,  2, 3, 14, 0, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 1 ), /* 93 */
    new tagLunarCal( 39,  0, 5, 20, 0, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1 ),
    new tagLunarCal( 28,  7, 6, 25, 1, 0, 1, 0, 1, 0, 1, 0, 1, 1, 0, 1, 1 ),
    new tagLunarCal( 48,  0, 0, 30, 0, 0, 1, 0, 0, 1, 0, 1, 1, 1, 0, 1, 1 ),
    new tagLunarCal( 37,  0, 1, 35, 1, 0, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1, 1 ), /* 97 */
    new tagLunarCal( 25,  5, 3, 41, 1, 1, 0, 0, 1, 0, 0, 1, 0, 1, 0, 1, 1 ),
    new tagLunarCal( 44,  0, 4, 46, 1, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1, 1 ),
    new tagLunarCal( 33,  0, 5, 51, 1, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1 ),
    new tagLunarCal( 22,  4, 6, 56, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0 ), /* 101 */
    new tagLunarCal( 40,  0, 1,  2, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0 ),
    new tagLunarCal( 30,  9, 2,  7, 0, 1, 0, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1 ),
    new tagLunarCal( 49,  0, 3, 12, 0, 1, 0, 0, 1, 0, 1, 1, 1, 0, 1, 0, 1 ),
    new tagLunarCal( 38,  0, 4, 17, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1, 1, 0 ), /* 105 */
    new tagLunarCal( 27,  6, 6, 23, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1, 1, 1 ),
    new tagLunarCal( 46,  0, 0, 28, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1, 1, 0 ),
    new tagLunarCal( 35,  0, 1, 33, 0, 1, 1, 0, 1, 0, 0, 1, 0, 0, 1, 1, 0 ),
    new tagLunarCal( 24,  4, 2, 38, 0, 1, 1, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1 ), /* 109 */
    new tagLunarCal( 42,  0, 4, 44, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1 ),
    new tagLunarCal( 31,  0, 5, 49, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0 ),
    new tagLunarCal( 21,  2, 6, 54, 0, 1, 0, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1 ),
    new tagLunarCal( 40,  0, 0, 59, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1, 1, 0, 1 ), /* 113 */
    new tagLunarCal( 28,  6, 2,  5, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1, 1, 1, 0 ),
    new tagLunarCal( 47,  0, 3, 10, 1, 0, 1, 0, 0, 1, 0, 0, 1, 1, 1, 0, 1 ),
    new tagLunarCal( 36,  0, 4, 15, 1, 1, 0, 1, 0, 0, 1, 0, 0, 1, 1, 0, 1 ),
    new tagLunarCal( 25,  5, 5, 20, 1, 1, 1, 0, 1, 0, 0, 1, 0, 0, 1, 1, 0 ), /* 117 */
    new tagLunarCal( 43,  0, 0, 26, 1, 1, 0, 1, 0, 1, 0, 1, 0, 0, 1, 0, 1 ),
    new tagLunarCal( 32,  0, 1, 31, 1, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 0 ),
    new tagLunarCal( 22,  3, 2, 36, 0, 1, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1, 0 ) ];
    /* 民國年每月之日數 */
    SolarCal = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];
    /* 民國年每月之累積日數, 平年與閏年 */ 
    SolarDays = [  0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365, 396,  0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366, 397 ];
    AnimalIdx = ["马", "羊", "猴", "鸡", "狗", "猪", "鼠", "牛", "虎", "兔", "龙", "蛇" ];
    LocationIdx = [ "南", "东", "北", "西" ];
    if ( year <= FIRSTYEAR || year > LASTYEAR ) return 1;
    sm = month - 1;
    if ( sm < 0 || sm > 11 ) return 2;
    leap = GetLeap( year );
    if ( sm == 1 )
    d = leap + 28;
    else
    d = SolarCal[sm];
    if ( date < 1 || date > d ) return 3;
    y = year - FIRSTYEAR;
    acc = SolarDays[ leap*14 + sm ] + date;
    kc = acc + LunarCal[y].BaseKanChih;
    Kan = kc % 10;
    Chih = kc % 12;
    Location = LocationIdx[kc % 4];
    Age = kc % 60;
    if ( Age < 22 )
    Age = 22 - Age;
    else
    Age = 82 - Age;
    Animal = AnimalIdx[ Chih ];
    if ( acc <= LunarCal[y].BaseDays ) {
    y--;
    LunarYear = year - 1;
    leap = GetLeap( LunarYear );
    sm += 12;
    acc = SolarDays[leap*14 + sm] + date;
    }
    else
    LunarYear = year;
    l1 = LunarCal[y].BaseDays;
    for ( i=0; i<13; i++ ) {
    l2 = l1 + LunarCal[y].MonthDays[i] + 29;
    if ( acc <= l2 ) break;
    l1 = l2;
    }
    LunarMonth = i + 1;
    LunarDate = acc - l1;
    im = LunarCal[y].Intercalation;
    if ( im != 0 && LunarMonth > im ) {
    LunarMonth--;
    if ( LunarMonth == im ) LunarMonth = -im;
    }
    if ( LunarMonth > 12 ) LunarMonth -= 12;
    var months = ["正","二","三","四","五","六","七","八","九","十","十一","腊"];
    var days = ["初一","初二","初三","初四","初五","初六","初七","初八","初九","初十","十一","十二","十三","十四","十五","十六","十七","十八","十九","二十","廿一","廿二","廿三","廿四","廿五","廿六","廿七","廿八","廿九","三十"];
    if (LunarMonth < 0) {
    LunarMonth = "闰" + months[-LunarMonth-1];
    }else{
    LunarMonth = months[LunarMonth-1];
    }
    LunarDate = days[LunarDate-1];
    var timeValue = "" +((hour >= 12) ? (hour >= 18) ? "晚上" : "下午" : "上午" ); //当前时间属于上午、晚上还是下午
    newDate = dateFilter(year)+"年"+dateFilter(month)+"月"+dateFilter(date)+"日【农历" + LunarMonth + "月" + LunarDate + "】"+dateFilter(hour)+":"+dateFilter(minute)+":"+dateFilter(second);
    document.getElementById("nowTime").innerHTML = "当前时间："+newDate+" "+week;
    document.getElementById("main_hello").innerHTML = "，"+timeValue+"好！";
    setTimeout("getLangDate()",1000);
}

/* 求此民國年是否為閏年, 返回 0 為平年, 1 為閏年 */
function GetLeap( year ) {
    if ( year % 400 == 0 )
    return 1;
    else if ( year % 100 == 0 )
    return 0;
    else if ( year % 4 == 0 )
    return 1;
    else
    return 0;
}
function tagLunarCal( d, i, w, k, m1, m2, m3, m4, m5, m6, m7, m8, m9, m10, m11, m12, m13) {
    this.BaseDays = d;         /* 到民國 1 月 1 日到農曆正月初一的累積日數 */
    this.Intercalation = i;    /* 閏月月份. 0==此年沒有閏月 */
    this.Baseday = w;      /* 此年民國 1 月 1 日為星期幾再減 1 */
    this.BaseKanChih = k;      /* 此年民國 1 月 1 日之干支序號減 1 */
    this.MonthDays = [ m1, m2, m3, m4, m5, m6, m7, m8, m9, m10, m11, m12, m13 ]; /* 此農曆年每月之大小, 0==小月(29日), 1==大月(30日) */
}