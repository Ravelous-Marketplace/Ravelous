<link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300" rel="stylesheet">
<style>
html, body {
    padding: 0;
    margin: 0;
}
div.header-fixed {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 3.75rem;
    width: 100%;
    overflow: hidden;
    z-index: 1000
}
div.header-fixed.menu-active {
    height: 100%;
}
div.header-moved {
    height: 3.75rem;
    width: 100%;
}
header {
    width: 100%;
    height: 100%;
    position: relative;
}
header > nav > div > div, header > nav > div > nav {
    float: left;
}
header > nav {
    height: 3.75rem;
    position: relative;
    box-sizing: border-box;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    border-bottom: 1px solid #E0DEEA;
    background-color: white;
    padding: 0 3.75rem;
}
header > nav > div.left-corner {
    float: left;
    box-sizing: initial;
}
header > nav > div.left-corner > *:first-child {
    border-left: 1px solid #E0DEEA;
}
header > nav > div.right-corner {
    float: right;
}
header > nav > div.right-corner > *:first-child {
    border-right: 1px solid #E0DEEA;
}
header > nav > div.right-corner input {
    height: calc(1.25rem - 1px);
}
header > nav a, header > nav input {
    position: relative;
    padding: 1.25rem;
    height: 1.25rem;
    border: none;
    text-decoration: none;
    z-index: 1000;
}
header > nav a span {
    font-size: 1.25rem;
    font-weight: 900;
    font-family: 'Open Sans Condensed', sans-serif;
    line-height: 1.25rem;
    text-transform: uppercase;
    color: black;
    transition: 0.2s;
}
header > nav > div.left-corner a {
    border-right: 1px solid #E0DEEA;
    float: left;
}
header > nav > a.center-logo {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    box-sizing: initial;
}
header > nav > div.right-corner a, header > nav > div.right-corner input {
    border-left: 1px solid #E0DEEA;
    float: left;
    box-sizing: initial;
}
header > nav > a > img {
    height: 100%;
}
.center-logo {
    display: block;
}
.mobile-logo {
    display: none;
    padding: 0;
    height: 100%;
}
header > nav > div a:after, header > nav .btn-menu:after, header > .menu > a:not(.menu-logo):after {
    content: '';
    height: 0;
    width: 100%;
    position: absolute;
    left: 0;
    bottom: 0;
    background-color: black;
    z-index: -1;
    transition: 0.2s cubic-bezier(0.76, 0.03, 0.44, 0.99);
}
header > nav > div a:hover span, header > .menu > a:hover span {
    color: white;
}
header > nav > div a:hover:after, header > nav .btn-menu:hover:after, header > .menu > a:not(.menu-logo):hover:after {
    height: 100%;
}
header > nav .btn-menu {
    width: 2rem;
    height: 1.25rem;
    padding: 1.25rem;
    border-right: 1px solid #E0DEEA;
    position: relative;
    z-index: 1000;
}
header > nav .btn-menu:before {
    content: "";
    position: absolute;
    left: 1.25rem;
    top: 1.25rem;
    width: 2rem;
    height: 0.25rem;
    background: black;
    box-shadow: 
      0 0.5rem 0 0 black,
      0 1rem 0 0 black;
    transition: 0.2s;
}
header > nav .btn-menu:hover:before {
    background: white;
    box-shadow: 
      0 0.5rem 0 0 white,
      0 1rem 0 0 white;
}

header > .menu {
    height: 100%;
    width: 15rem;
    background-color: white;
    position: absolute;
    top: 0;
    bottom: 0;
    left: -15rem;
    border-right: 1px solid #E0DEEA;
    z-index: 1100;
    transition: 0.2s cubic-bezier(0.76, 0.03, 0.44, 0.99);
}
header > .menu > a > img {
    width: 100%;
}
header > .menu > a {
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    text-decoration: none;
    border-bottom: 1px solid #E0DEEA;    
    float: left;
    width: 100%;
    padding: 1rem;    
    position: relative;
}
header > .menu > a span {
    color: black;
    transition: 0.3s;
    text-transform: uppercase;
    font-family: 'Open Sans Condensed', sans-serif;
    font-size: 1.3rem;
    font-weight: 900;
    line-height: 1.3rem;
}
header > .menu.menu-active {
    left: 0;
}
header:after {
    content: '';
    width: 0;
    height: 100%;
    position: absolute;
    left: 0;
    top: 0;
    background-color: rgba(0,0,0,0.7);
    z-index: 1050;
    transition: 0.2s cubic-bezier(0.76, 0.03, 0.44, 0.99);
}

#headerSearchInput::-webkit-input-placeholder,
#headerSearchInput::-moz-placeholder,
#headerSearchInput:-ms-input-placeholder,
#headerSearchInput:-moz-placeholder {
    font-style: italic; 
}

header.menu-active:after {
    width: 100%;
}
@media screen and (max-width: 70rem) {
    header > nav {
        padding: 0 1rem;
    }
}
@media screen and (max-width: 62.5rem) {
    .center-logo {
        float: left;
        position: relative !important;
        transform: none !important;
        left: 0 !important;
    }
    header > nav .left-corner a:nth-of-type(2) {
        display: none;
    }
}
@media screen and (max-width: 53rem) {
    .center-logo {
        display: none;
    }
    .mobile-logo {
        display: inline-block;
    }
}
@media screen and (max-width: 43.75rem) {
    header > nav {
        padding: 0 !important;
    }
    header > nav > div.left-corner > .row > *:first-child {
        display: none;
    }
}
</style>
<script>
var windowWidth;
$(document).ready(function(){
    resizesearch();
    $(document).click(function(e) {
        if (!$(e.target).is('header > .menu') && !$(e.target).is('header > nav .btn-menu')) {
            $("header").removeClass("menu-active");
            $("header > .menu").removeClass("menu-active");
            setTimeout(function(){
                $(".header-fixed").removeClass("menu-active");
            }, 200);
        }
    });
    $("header > nav .btn-menu").click(function(){
        $("header").addClass("menu-active");
        $(".header-fixed").addClass("menu-active");
        $("header > .menu").addClass("menu-active");
    });
});

window.addEventListener("resize", function() {
    resizesearch();
}, false);

function resizesearch() {
    if (window.innerWidth < 700) {
        navWidth = $('header > nav').width();
        searchWidth = $('#headerSearchInput').width();
        usedWidth = 0;
        $('header > nav > *').each(function(){
            if ($(this).is(':visible')) {
                usedWidth += $(this).width();
            }
        });
        unusedWidth = navWidth - usedWidth;
        newSearchWidth = unusedWidth + searchWidth;
        $('#headerSearchInput').width(newSearchWidth);
    } else {
        $('#headerSearchInput').width(165);
    }
    
    if ($('#headerSearchInput').width() < 165) {
        $('#headerSearchInput').attr('placeholder', 'Search');
    } else {
        $('#headerSearchInput').attr('placeholder', 'Search the cryptoverse');
    }
}

</script>

<div class="header-fixed">
    <header>
        <div class="menu">
            <a href="/" class="menu-logo"><img src="/img/ravelous.png"/></a>
            <a href="/"><span>Home</span></a>
            <?php
            if (isset($_SESSION['id']) && is_numeric($_SESSION["id"])) {
                echo '<a href="/account"><span>Account</span></a><a href="/account/?logout"><span>Log Out</span></a>';
            } else {
                echo '<a href="/login"><span>Log In / Sign Up</span></a>';
            }
            ?>
        </div>
        <nav>
            <div class="left-corner">
                <div class="btn-menu">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <nav class="row">
                    <?php
                    if (isset($_SESSION['id']) && is_numeric($_SESSION["id"])) {
                        echo '<a href="/account"><span>Account</span></a><a href="/account/?logout"><span>Log Out</span></a>';
                    } else {
                        echo '<a href="/login"><span>Log In / Sign Up</span></a>';
                    }
                    ?>
                </nav>
            </div>
            <a href="/" class="center-logo"><img src="/img/ravelous.png"/></a>
            <a href="/" class="mobile-logo"><img src="/img/ravelous_icon.jpg"/></a>
            <div class="right-corner">
                <nav class="row">
                    <input id="headerSearchInput" type="text" placeholder="Search the cryptoverse"/>
                    <a id="headerSearch"><span><i class="material-icons">search</i></span></a>
                    <a href=""><span><i class="material-icons">shopping_cart</i></span></a>
                </nav>
            </div>
        </nav>
    </header>
</div>
<div class="header-moved"></div>