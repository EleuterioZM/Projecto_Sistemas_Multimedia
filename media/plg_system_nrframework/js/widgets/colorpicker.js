document.addEventListener("input",function(t){var e=t.target.closest(".nrf-colorpicker-wrapper");if(e){var r=t.target.closest('input[type="color"]');r&&(e.querySelector('input[type="text"]').value=r.value);var a=t.target.closest('input[type="text"]');if(a){var o=a.value;o.startsWith("#")||(o="#"+o),e.querySelector('input[type="color"]').value=o}}});

