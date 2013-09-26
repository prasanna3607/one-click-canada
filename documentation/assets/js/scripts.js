eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('4(c).8(2($){(2($,a){$(b).1(2(){6($(7).5()>9){$(\'#1-3\').n()}d{$(\'#1-3\').f()}});$(\'#1-3\').g();$(\'#1-3 a\').h(2(e){e.j();$(\'k,l\').m({5:0},i)})})(4)});',24,24,'|scroll|function|top|jQuery|scrollTop|if|this|ready|100||window|document|else||fadeOut|hide|click|600|preventDefault|body|html|animate|fadeIn'.split('|'),0,{}))

$(document).ready(function() {
    $('#mainNav a').click(function(ev) {
        ev.preventDefault();
        var $this = $(this);
        
        $('html, body').animate({
            scrollTop: $($this.attr('href')).offset().top
        });
    });
    
    $(window).scroll(function() {
        if( $(window).scrollTop() > $('#main-content').offset().top) {
            $('#mainNav').addClass('fixed');
        } else {
            $('#mainNav').removeClass('fixed');
        }
    });
    
});
