;(function () {

    var standardWidth = 375;
    var standardFontSize = 12;

    $('html').attr({'data-dpr': 2})
        .css({
            'font-size': $(window).width() / standardWidth * standardFontSize + 'px',
            width: '100%',
            height: '100%',
           
        })
})()

if (window.orientation === 180 || window.orientation === 0) { 
 var standardFontSize = 12;
  font_size()
} 

if (window.orientation === 90 || window.orientation === -90 ) { 
  var standardFontSize = 6.5;
  font_size()
} 


//竖屏字体大小计算
function font_size() {

	 ;(function () {

    var standardWidth = 375;
    

    $('html').attr({'data-dpr': 1})
        .css({
            'font-size': $(window).width() / standardWidth * standardFontSize + 'px',
            width: '100%',
            height: '100%',
           
        })
     })()

}
