"use strict";

var KTWidgets = {
    init: function (chartConfig) {
        // Obtener los datos del controlador utilizando Ajax
        $.ajax({
            url: chartConfig.url, // Ruta dinámica hacia el controlador que obtiene los datos
            method: 'GET',
            success: function(response) {
                // Renderizar el gráfico con los datos obtenidos
                var a = document.getElementById(chartConfig.chartElementId);
                var o = parseInt(KTUtil.css(a, "height"));
                var s = KTUtil.getCssVariableValue("--bs-gray-500");
                var r = KTUtil.getCssVariableValue("--bs-gray-200");
                var i = KTUtil.getCssVariableValue("--bs-gray-400");
                var l = KTUtil.getCssVariableValue("--bs-success");
                var m = KTUtil.getCssVariableValue("--bs-danger");

                new ApexCharts(a, {
                    series: response.series,
                    chart: {
                        fontFamily: "inherit",
                        type: "bar",
                        height: o,
                        toolbar: { show: !1 }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: !1,
                            columnWidth: ["40%"],
                            borderRadius: 4
                        }
                    },
                    legend: { show: !1 },
                    dataLabels: { enabled: !1 },
                    stroke: { show: !0, width: 2, colors: ["transparent"] },
                    xaxis: {
                        categories: response.categories,
                        axisBorder: { show: !1 },
                        axisTicks: { show: !1 },
                        labels: { style: { colors: s, fontSize: "12px" } }
                    },
                    yaxis: { labels: { style: { colors: s, fontSize: "12px" } } },
                    fill: { opacity: 1 },
                    states: {
                        normal: { filter: { type: "none", value: 0 } },
                        hover: { filter: { type: "none", value: 0 } },
                        active: { allowMultipleDataPointsSelection: !1, filter: { type: "none", value: 0 } }
                    },
                    tooltip: {
                        style: { fontSize: "12px" },
                        y: {
                            formatter: function (e) {
                                return e + " " + chartConfig.tooltipSuffix;
                            }
                        }
                    },
                    colors: [l, m, i],
                    grid: { borderColor: r, strokeDashArray: 4, yaxis: { lines: { show: !0 } } }
                }).render();
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }
};
