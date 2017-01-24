$(document).ready(function(){

    drawGraphic(function () {
        $('#container_graph').text('Начислений нет');
    });
    drawDiagram($('#detail_select').val());

    $('input[name="daterange"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Отменить',
            applyLabel: 'Применить',
        }

    }, function(start, end, label) {
        graphicData.graphicPage = 0;
        graphicData.startDate = start.format('YYYY-MM-DD');
        graphicData.endDate = end.format('YYYY-MM-DD');

        drawGraphic(function () {
            $('#container_graph').text('Не найдено начислений в данном диапозоне');
        });

    });

    $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM.DD.YYYY') + ' - ' + picker.endDate.format('MM.DD.YYYY'));
    });

    $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    $('#detail_select').change(function () {
        drawDiagram($(this).val())
    });

    $('#graphic_select').change(function () {
        graphicData.graphicPage = 0;
        drawGraphic();
    });

    $('#graphic_prev').click(function () {

        graphicData.graphicPage++;
        changePage();
    });

    $('#graphic_next').click(function () {

        graphicData.graphicPage--;
        changePage();
    });



});

var graphicData = {
    graphicPage: 0,
    start_date: 0,
    end_date: 0
};

function changePage()
{
    if (graphicData.graphicPage < 0) {
        graphicData.graphicPage = 0;
        return false;
    }
    drawGraphic(function () {
        graphicData.graphicPage--;
    })
}

function drawGraphic(empty)
{

    var graphic_detail = $('#graphic_select').val();
    $.ajax({
        url: "/rebate/balance/index",
        type: "post",
        dataType: 'json',
        data: {
            graphic_detail: graphic_detail,
            start_date: graphicData.startDate,
            end_date: graphicData.endDate,
            graphic_page: graphicData.graphicPage
        },
        success: successGraphic(empty),
        error: error
    });

}

function drawDiagram(detailType)
{
    var detail = detailType;
    $.ajax({
        url: "/rebate/balance/index",
        type: "post",
        dataType: 'json',
        data: {
            detail: detail
        },
        success: function(data){
            if (data.length != 0) {
                printDiagram(data);
            } else {
                $('#container_diagram').text('Начислений нет');
            }
        },
        error: function () {
            alert('ajax error');
        }
    });

}

function printGraphic(data)
{
    var categories = data['cat'];
    var values = data['val'];
    var labels = {};
    if (categories.length > 10) {
        labels = {
            rotation: -90,
            style: {
                fontSize: '12px',
                fontFamily: 'Verdana, sans-serif'
            }
        };
    }

    Highcharts.chart('container_graph', {
        chart: {
            type: 'areaspline'
        },
        title: {
            text: ''
        },
        legend: false,
        xAxis: {
            categories: categories,
            labels: labels
        },
        yAxis: {
            title: {
                text: ''
            }
        },
        tooltip: {
            valueSuffix: ' $'
        },
        credits: {
            enabled: false
        },
        plotOptions: {
            areaspline: {
                fillOpacity: 0.5
            }
        },
        series: [{
            name: 'Начислено',
            data: values
        }]

    });
}

function printDiagram(data)
{
    Highcharts.chart('container_diagram', {
        credits: {enabled: 0},
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '<b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: false
            }
        },
        series: [{
            name: 'детализация',
            colorByPoint: true,
            data: data
        }]
    });
}

function successGraphic(emptyData)
{
    return function(data)
    {
        if (data.length != 0) {
            printGraphic(data);
            return true;
        } else {
            if(emptyData) {
                emptyData();
            }
            return false;
        }
    }
}

function error() {
    alert('ajax error');
    return false;
}
