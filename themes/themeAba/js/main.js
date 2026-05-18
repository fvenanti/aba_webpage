(function ($, root, undefined) {
    
    $(function () {
        
        'use strict';
        
        // DOM ready, take it away

        $('#menuResp').slicknav();

        var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            paginationClickable: true,
            slidesPerView: 4,
            spaceBetween: 0,
            autoplay: 3500,
            autoplayDisableOnInteraction: true,
            loop: true,
            breakpoints: {
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 0
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 0
                },
                640: {
                    slidesPerView: 1,
                    spaceBetween: 0
                },
                320: {
                    slidesPerView: 1,
                    spaceBetween: 0
                }
            }
        });
        //ANCHOR LINK
        $('a[href*="#"]:not([href="#"])').click(function() {
            if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
              var target = $(this.hash);
              target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
              if (target.length) {
                $('html, body').animate({
                  scrollTop: target.offset().top
                }, 1000);
                return false;
              }
            }
        });

      //Google conversion

       /* var gButton = $("#gphone");
        var gButton2 = $("#gmail");
        var url = "https://wa.me/5492944604766";
        var url2 = "mailto:info@abarentacar.com.ar";
        
        gButton.on("click", function(){ gtag_report_conversion(url); });
        gButton2.on("click", function(){ gtag_report_conversion(url2); });*/

        $("#gphone").attr('onclick',"return gtag_report_conversion('https://wa.me/5492944604766')");
        $("#gmail").attr('onclick',"return gtag_report_conversion('mailto:info@abarentacar.com.ar')");
            
         
        
    });
    
})(jQuery, this);


