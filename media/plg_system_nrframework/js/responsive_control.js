document.addEventListener("DOMContentLoaded",function(){document.addEventListener("click",function(e){var t=e.target.closest(".nr-responsive-control-type-btn");if(t){if(t.classList.contains("is-active"))return!1;var s=t.closest(".nr-responsive-control");s.querySelector(".top .actions a.is-active").classList.remove("is-active"),t.classList.add("is-active"),s.querySelector(".content .item.is-active").classList.remove("is-active");var i=t.dataset.type;s.querySelector(".content .item."+i).classList.add("is-active"),e.preventDefault()}})});
