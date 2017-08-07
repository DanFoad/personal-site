    </main>

    <div class="smooth-scroll to-top" data-to="body"><i class="fa fa-angle-up"></i></div>
    
    <footer>
        <section class="container">
            <a href="/"><img src="/favicon-32x32.png" alt="" /></a>
            &copy; 2017 - Dan Foad

            <div class="nav__container">
                <ul>
                    <li><a href="http://danfoad.co.uk">Home</a></li>
                    <li><a href="http://danfoad.co.uk#contact">Contact Me</a></li>
                </ul>
            </div>
        </section>
    </footer>
    
    <script>
        hljs.initHighlightingOnLoad();
        $(document).ready(function() {
            lineCode();
        });

        function lineCode() {
            var pre = document.getElementsByTagName('pre'),
                pl = pre.length;
            for (var i = 0; i < pl; i++) {
                pre[i].innerHTML = '<span class="line-number"></span>' + pre[i].innerHTML + '<span class="cl"></span>';
                var num = pre[i].innerHTML.split(/\n|<br>/).length;
                for (var j = 0; j < num; j++) {
                    var line_num = pre[i].getElementsByTagName('span')[0];
                    line_num.innerHTML += '<span>' + (j + 1) + '</span>';
                }
            }
        }
        $(".header__nav--fixed").show();
        
    </script>
</body>
</html>