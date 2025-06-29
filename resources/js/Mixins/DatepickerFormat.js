export function DatepickerFormat(date, format=false){
    const days = ['', '01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'];
    const months = ['01','02','03','04','05','06','07','08','09','10','11','12'];
    let retval={from:false, to:false};
    let day1='', month1='', year1='', hours1='', mins1='', day2='', month2='', year2='', hours2='', mins2='';

    //TODO date.toLocaleString("ru", {
    //    year: 'numeric',
    //    month: 'numeric',
    //    day: 'numeric',
    //    timezone: 'UTC+3'
    // });

    if(date[0]==undefined && date!=null){
        retval.from=true;
        day1 = days[date.getDate()];
        month1 = months[date.getMonth()];
        year1 = date.getFullYear();
        return `${day1}.${month1}.${year1}`;
    }

    if(date[0]!=null && date[0]!=undefined){
        retval.from=true;
        day1 = days[date[0].getDate()];
        month1 = months[date[0].getMonth()];
        year1 = date[0].getFullYear();
        hours1 = date[0].getHours();
        mins1 = date[0].getMinutes();
    }

    if(date[1]!=null && date[1]!=undefined){
        retval.to=true;
        day2 = days[date[1].getDate()];
        month2 = months[date[1].getMonth()];
        year2 = date[1].getFullYear();
        hours2 = date[1].getHours();
        mins2 = date[1].getMinutes();
    }

    if (!retval.from && !retval.to) return ``;

    if(format=='dmy'){
        if (retval.from && retval.to) return `${day1}.${month1}.${year1} - ${day2}.${month2}.${year2}`;
        if (retval.from && !retval.to) return `${day1}.${month1}.${year1}`;
        if (!retval.from && retval.to) return ` - ${day2}.${month2}.${year2}`;
    }
    else{
        if (retval.from && retval.to) return `${day1}.${month1}.${year1} ${hours1}:${mins1} - ${day2}.${month2}.${year2}, ${hours2}:${mins2}`;
        if (retval.from && !retval.to) return `${day1}.${month1}.${year1} ${hours1}:${mins1}`;
        if (!retval.from && retval.to) return ` - ${day2}.${month2}.${year2}, ${hours2}:${mins2}`;
    }
}
