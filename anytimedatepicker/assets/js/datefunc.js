Date.createFromMysql=function(a){if(typeof a==="string")return a=a.split(/[- :]/),new Date(a[0],a[1]-1,a[2],a[3]||0,a[4]||0,a[5]||0);return null};
