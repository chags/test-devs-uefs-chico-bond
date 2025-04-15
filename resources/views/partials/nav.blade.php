<nav id="header" class="fixed top-0 z-30 w-full text-white">
    <div class="container flex flex-wrap items-center justify-between w-full py-2 mx-auto mt-0">
        <div class="flex items-center pl-4">
            <a class="text-2xl font-bold text-white no-underline toggleColour hover:no-underline lg:text-4xl" href="#">
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAAB6UlEQVR4nO2Zu0oDQRSGv8RLVBRUEEVstNJKKwmYF7CwtLW09RG0tPCKPoEvYOELBBUEIQgWYgqx8K4RI4IgXkYSTmDYOGY35jKJ88GBZThz5p/Z+Qd2Fhz/hwgwC5wCSXnOtNUMHcAccAkoT9wBC0AXFtMHLAJpTXgCmJZIaO1pyc30sYZBYB141YTuA1NAyJMbA3a0vDdgCximioyKiHcR9Skioz76jknfD0/fcSpIblW/PKs6UkStoV/eZlkIS/EDbcAXETFQgvq9cgg8afWPgBmgoQT1aZZiJ9oA9zJoN+U78a608c6kraWYgu3S+UIreC5tbZSfiCxgUhv/Vhaw00+BHkl+1AocS9FGKk9YtvShpudZtnS/qdO8x3RxYPKHI7QahERLXNP3KprzyB2F2z6P0GoRFY1KNOehajTy2LNAlAoYuxTgQRInsIeYdvz7ZtmC1VaGWAoykSZLJ7Ms2gLjNZTJYNXK842bCO6NZHFby4TzCM4jWZxHTDiP4DySxXnEhPMIziNZnEfq1iPNwIYFn7bKExuizTdrFohWhlgNMpHcdVBFf7z4uGEMfB3054/9MqH86GoFNoGUBdtHFYiU+OXHfyYrFghUpbisu7HQF4X8cl1LvjBh1KtqNPKom4k4HJSOb9YIPbxbHjdLAAAAAElFTkSuQmCC" alt="university">
            UEFS
            </a>
        </div>
        <div class="block pr-4 lg:hidden">
            <button id="nav-toggle"
                class="flex items-center px-3 py-2 text-gray-500 border border-gray-600 rounded appearance-none hover:text-gray-800 hover:border-teal-500 focus:outline-none">
                <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" />
                </svg>
            </button>
        </div>
        <div class="z-20 flex-grow hidden w-full p-4 mt-2 text-black bg-white lg:flex lg:items-center lg:w-auto lg:block lg:mt-0 lg:bg-transparent lg:p-0"
            id="nav-content">
            <ul class="items-center justify-end flex-1 list-reset lg:flex">
                <li class="mr-3">
                    <a class="inline-block px-4 py-2 font-bold text-black no-underline" href="#">Home</a>
                </li>
                <li class="mr-3">
                    <a class="inline-block px-4 py-2 text-black no-underline hover:text-gray-800 hover:text-underline"
                        href="#posts">Posts</a>
                </li>
            </ul>
            <a id="navAction" href="{{ url('/api/documentation') }}" target="_blank"
                class="px-8 py-4 mx-auto mt-4 font-bold text-gray-800 bg-white rounded-full shadow opacity-75 lg:mx-0 hover:underline lg:mt-0">
                API SWAGGER
            </a>
        </div>
    </div>
    <hr class="py-0 my-0 border-b border-gray-100 opacity-25" />
</nav>
<script>
    var scrollpos = window.scrollY;
    var header = document.getElementById("header");
    var navcontent = document.getElementById("nav-content");
    var navaction = document.getElementById("navAction");
    var toToggle = document.querySelectorAll(".toggleColour");
    document.addEventListener('scroll', function () {
        scrollpos = window.scrollY;
        if (scrollpos > 10) {
            header.classList.add("bg-white");
            navaction.classList.remove("bg-white");
            navaction.classList.add("gradient");
            navaction.classList.remove("text-gray-800");
            navaction.classList.add("text-white");
            for (var i = 0; i < toToggle.length; i++) {
                toToggle[i].classList.add("text-gray-800");
                toToggle[i].classList.remove("text-white");
            }
            header.classList.add("shadow");
            navcontent.classList.remove("bg-gray-100");
            navcontent.classList.add("bg-white");
        } else {
            header.classList.remove("bg-white");
            navaction.classList.remove("gradient");
            navaction.classList.add("bg-white");
            navaction.classList.remove("text-white");
            navaction.classList.add("text-gray-800");
            for (var i = 0; i < toToggle.length; i++) {
                toToggle[i].classList.add("text-white");
                toToggle[i].classList.remove("text-gray-800");
            }
            header.classList.remove("shadow");
            navcontent.classList.remove("bg-white");
            navcontent.classList.add("bg-gray-100");
        }
    });
</script>