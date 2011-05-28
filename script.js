function pcredits_mouseover(evt,date, id) {
  if(!evt) evt = document.event;

var top;
var left;

  if(evt.clientX) {
     top = evt.clientY;
     left = evt.clientX;  
  }
  else if(e.pageX) {
    left= e.pageX;
    top = e.pageY;
  }
  else {
        return;
  }
    top -= 32;
    left -= 24;

   
    if(date.length > 100) {
//       left = 20;         
    }

    if(document.body.scrollLeft || document.body.scrollTop) {
         top+=document.body.scrollTop;         
         left += document.body.scrollLeft;
    }
    else if(document.documentElement.scrollTop || document.documentElement.scrollLeft) {
         top+=document.documentElement.scrollTop;         
         left += document.documentElement.scrollLeft;
    }
    else if(window.pageXOffset || window.pageYOffset) {
         top+=window.pageYOffset;         
         left += window.pageXOffset;
    }

    dom = document.getElementById('pcredit_mo');
    dom.innerHTML = date;   
    dom.style.visibility="visible";
    dom = document.getElementById('pcredit_mo');    
    dom.style.top = top + "px"; 
    dom.style.left = left + "px"; 
    dom.style.visibility="visible";
    if(id) {            
         show_remainder(id)
    }



}

function show_remainder(id) {

            remainder = document.getElementById(id);                
            remainder.style.display = 'block';

}

function hide_remainder(id) {
            remainder = document.getElementById(id);                
            remainder.style.display = 'none';

}

function pcredits_mouseout(id) {
    dom = document.getElementById('pcredit_mo');
    dom.style.visibility="hidden";
    if(id) {
           hide_remainder(id); 
    }

}
