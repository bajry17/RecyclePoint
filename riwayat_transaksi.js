
document.addEventListener("DOMContentLoaded", function () {
    const tabs = document.querySelectorAll(".rt-tab");
    const rows = document.querySelectorAll("#rtTableBody tr");
    const emptyMessage = document.getElementById("rtEmpty");
    const status = document.getElementById("rtStatus");

    tabs.forEach(function (tab) {
        tab.addEventListener("click", function () {
            const filter = tab.dataset.filter;
            let visibleCount = 0;

            tabs.forEach(function (item) {
                item.classList.remove("active");
                item.setAttribute("aria-pressed", "false");
            });

            tab.classList.add("active");
            tab.setAttribute("aria-pressed", "true");

            rows.forEach(function (row) {
                const type = row.dataset.type;
                const show = filter === "semua" || filter === type;

                row.hidden = !show;

                if (show) {
                    visibleCount++;
                }
            });

            emptyMessage.hidden = visibleCount !== 0;

            status.textContent = visibleCount + " transaksi ditampilkan.";
        });
    });
});