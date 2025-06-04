export function PhoneFormat(x=''){

    if(x=='') return '';

    if(x.length>2){x=x.replace(/[^\d]/g, '');}

    if(x[0]!='+'){x='+' + x;}

    if(x[1]!='7' && x.length>2){x='+7' + x.slice(1);}

    if(x.length>12) return x.slice(0, 12);
    else return x;
    
}