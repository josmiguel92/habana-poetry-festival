import { Controller } from 'stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    connect() {

        /*=====================================
        Sticky
        ======================================= */
        window.onscroll = function () {
            var header_navbar = document.querySelector(".navigation");
            var sticky = header_navbar.offsetTop;

            if (window.pageYOffset > sticky) {
                header_navbar.classList.add("sticky");
            } else {
                header_navbar.classList.remove("sticky");
            }



            // show or hide the back-top-top button
            var backToTop = document.querySelector(".back-to-top");
            if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
                backToTop.style.display = "flex";
            } else {
                backToTop.style.display = "none";
            }
        };




        // for menu scroll
        var pageLink = document.querySelectorAll('.page-scroll');

        pageLink.forEach(elem => {
            elem.addEventListener('click', e => {
                e.preventDefault();
                document.querySelector(elem.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth',
                    offsetTop: 1 - 60,
                });
            });
        });


        // section menu active
        function onScroll(event) {
            var sections = document.querySelectorAll('.page-scroll');
            var scrollPos = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;

            for (var i = 0; i < sections.length; i++) {
                var currLink = sections[i];
                var val = currLink.getAttribute('href');
                var refElement = document.querySelector(val);
                var scrollTopMinus = scrollPos + 73;
                if (refElement.offsetTop <= scrollTopMinus && (refElement.offsetTop + refElement.offsetHeight > scrollTopMinus)) {
                    document.querySelector('.page-scroll').classList.remove('active');
                    currLink.classList.add('active');
                } else {
                    currLink.classList.remove('active');
                }
            }
        };

        window.document.addEventListener('scroll', onScroll);

        //===== close navbar-collapse when a  clicked
        let navbarToggler = document.querySelector(".navbar-toggler");
        var navbarCollapse = document.querySelector(".navbar-collapse");

        document.querySelectorAll(".page-scroll").forEach(e =>
            e.addEventListener("click", () => {
                navbarToggler.classList.remove("active");
                navbarCollapse.classList.remove('show')
            })
        );
        navbarToggler.addEventListener('click', function () {
            navbarToggler.classList.toggle("active");
            navbarCollapse.classList.toggle('show')
        })

    }
}
