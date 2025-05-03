$(function () {
    var chart = {
        series: [
            {
                name: "Jumlah Pemeriksaan",
                data: pemeriksaanData, // Data dari backend
            },
        ],

        chart: {
            type: "bar",
            height: 345,
            offsetX: -15,
            toolbar: { show: true },
            foreColor: "#adb0bb",
            fontFamily: "inherit",
            sparkline: { enabled: false },
        },

        colors: ["#5D87FF"],

        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: "35%",
                borderRadius: [6],
                borderRadiusApplication: "end",
                borderRadiusWhenStacked: "all",
            },
        },

        dataLabels: {
            enabled: false,
        },

        xaxis: {
            type: "category",
            categories: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "Jun",
                "Jul",
                "Aug",
                "Sep",
                "Oct",
                "Nov",
                "Dec",
            ],
        },

        yaxis: {
            show: true,
            min: 0,
            tickAmount: 5,
        },

        tooltip: { theme: "light" },
    };

    var chart = new ApexCharts(document.querySelector("#chart"), chart);
    chart.render();
});
