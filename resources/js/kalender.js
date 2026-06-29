import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

document.addEventListener("DOMContentLoaded", () => {

    const el = document.querySelector("#tanggal");

    if (el) {
        flatpickr(el, {
            inline: true,
            dateFormat: "Y-m-d",
            defaultDate: "today"
        });
    }

});