'use strict';

/* global Chart moment */

function getStatisticsData(name) {
    return $.parseJSON($('#' + name).attr('content'));
}

function getStatisticsDataYMDC(name, field) {
    return getStatisticsData(name).map(function mapItemToDate(item) {
        const d = new Date(item.day * 1000);
        d.setHours(0);
        return {
            x: d,
            y: item[field]
        };
    });
}

function getTranslation(str) {
    return $.parseJSON($('#translations').attr('content'))[str];
}

function extendData(data, minX, maxX) {
    let i = 0;
    const extendedData = [];
    for (let d = new Date(minX); d <= maxX; d.setDate(d.getDate() + 1),d.setHours(0)) {
        if (data[i].x.getTime() === d.getTime()) {
            extendedData.push(data[i]);
            i += 1;
        } else if (data[i].x.getTime() > d.getTime()) {
            extendedData.push({ x: new Date(d), y: 0 });
        } else {
            throw new Error("Data is not sorted");
        }
    }
    return extendedData;
}

function drawLoginsChart(getEl) {
    const el = getEl();
    if (!el) return;

    const ctx = el;

    const previousChart = Chart.getChart(ctx);
    if (previousChart) {
        previousChart.destroy();
    }

    let data = getStatisticsDataYMDC('loginCountPerDay', 'count');
    let data2 = getStatisticsDataYMDC('loginCountPerDay', 'users');

    if (!data.length || !data2.length) {
        $(el).parent().replaceWith('<p class="text-muted">No data has been recorded in the selected period...</p>');
        return;
    }

    const minX = Math.min(data[0].x, data2[0].x);
    const maxX = Math.max(data[data.length - 1].x, data2[data2.length - 1].x);

    data = extendData(data, minX, maxX);
    data2 = extendData(data2, minX, maxX);

    new Chart(ctx, { // eslint-disable-line no-new
        type: 'bar',
        options: {
            maintainAspectRatio: false,
            plugins: {
                tooltips: {
                    intersect: false,
                    mode: 'index',
                    callbacks: {
                        label: function showLabel(tooltipItem) {
                            let label = tooltipItem.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += tooltipItem.parsed;
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    type: 'time',
                    time: { // do not set round: 'day', because it breaks zooming out from 7 or fewer days
                        isoWeekday: true,
                        minUnit: 'day',
                        tooltipFormat: 'l'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: false
                    }
                }
            },
            zoom: {
                limits: {
                    x: {min: minX, max: maxX}
                },
                pan: {
                    enabled: true,
                    mode: 'x'
                },
                zoom: {
                    wheel: {
                        enabled: true
                    },
                    pinch: {
                        enabled: true
                    },
                    /*drag: {
                      enabled: false
                    },*/
                    mode: 'x'
                },
            }
        },
        "data": {
            "datasets": [
                {
                    label: getTranslation('of_users'),
                    data: data2,
                    type: 'line',
                    pointRadius: 0,
                    fill: false,
                    lineTension: 0,
                    borderWidth: 2,
                    backgroundColor: '#3b3eac',
                    borderColor: '#3b3eac'
                },
                {
                    label: getTranslation('of_logins'),
                    data: data,
                    type: 'line',
                    pointRadius: 0,
                    fill: false,
                    lineTension: 0,
                    borderWidth: 2,
                    backgroundColor: '#f90',
                    borderColor: '#f90'
                }
            ]
        }
    });
}

const pieColors = [
    '#3366CC', '#DC3912', '#FF9900', '#109618', '#990099', '#3B3EAC', '#0099C6', '#DD4477', '#66AA00', '#B82E2E',
    '#316395', '#994499', '#22AA99', '#AAAA11', '#6633CC', '#E67300', '#8B0707', '#329262', '#5574A6', '#3B3EAC'
];

const minPieFraction = 0.005;
const minPieOtherFraction = 0.01;
const maxPieOtherFraction = 0.20;
const pieOtherOnlyIfNeeded = false;

function processDataForPieChart(data, viewCols) {
    if (pieOtherOnlyIfNeeded && data.length <= pieColors.length) {
        return data;
    }
    const col = viewCols || [0, 1];
    const total = data.reduce(function getSum(accumulator, currentValue) {
        return accumulator + currentValue[col[1]];
    }, 0);
    let i = data.length;
    let othersFraction = 0;
    while (i > 1
    && (i > pieColors.length
        || (data[i - 1][col[1]] / total < minPieFraction
            && data[i - 1][col[1]] / total + othersFraction < maxPieOtherFraction))) {
        i -= 1;
        othersFraction += data[i][col[1]] / total;
    }
    if (othersFraction < minPieOtherFraction) {
        i = Math.min(data.length, pieColors.length);
        othersFraction = 0;
    }
    const processedData = data.slice(0, i);
    if (i < data.length && othersFraction > 0) {
        const theOthers = [null, null, null];
        theOthers[col[1]] = Math.round(othersFraction * total);
        theOthers[col[0]] = getTranslation('other');
        processedData.push(theOthers);
    }
    return { data: processedData, other: othersFraction > 0, total: total };
}

function drawPieChart(dataName, viewCols, url, getEl) {
    const el = getEl();
    if (!el) return;

    const ctx = el.getContext('2d');

    const previousChart = Chart.getChart(ctx);
    if (previousChart) {
        previousChart.destroy();
    }

    const processedData = processDataForPieChart(getStatisticsData(dataName), viewCols);
    const data = processedData.data;

    if (!data.length) {
        $(el).parent().parent().replaceWith('<p class="text-muted">No data has been recorded in the selected period...</p>');
        return;
    }

    const other = processedData.other;
    const total = processedData.total;
    const col = viewCols || [0, 1];
    const colors = pieColors.slice();
    if (other) {
        colors[data.length - 1] = '#DDDDDD';
    }
    const chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.map(function getFirst(row) {
                return row[col[0]];
            }),
            datasets: [{
                data: data.map(function getSecond(row) {
                    return row[col[1]];
                }),
                backgroundColor: colors,
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function generateLabel(tooltipItem) {
                            let label = tooltipItem.label || '';

                            if (label) {
                                label += ': ';
                            }
                            const value = tooltipItem.parsed;
                            label += value + ' (';
                            label += Math.round((value / total) * 1000) / 10;
                            label += ' %)';
                            return label;
                        }
                    }
                }
            }
        }
    });
    if (url) {
        el.addEventListener('click', function pieClick(evt) {
            const activePoints = chart.getElementsAtEventForMode(evt, 'nearest', {intersect: true}, false);
            if (activePoints.length) {
                window.location.href = url + encodeURIComponent(data[activePoints[0].index][1]);
            }
        });
    }
}

function getDrawChart(side) {
    return drawPieChart.bind(
        null,
        'loginCountPer' + side,
        [0, 2],
        getStatisticsData('module_url_base') + 'detail.php?side=' + side + '&id='
    );
}

function drawCountTable(cols, dataCol, countCol, dataName, allowHTML, url, getEl) {
    const el = getEl();
    if (!el) return;

    const viewCols = [dataCol, countCol];

    const tableDiv = el.appendChild(document.createElement('div'));
    tableDiv.className = 'table-responsive';

    const table = tableDiv.appendChild(document.createElement('table'));
    table.className = 'table table-striped table-hover table-condensed';

    const data = getStatisticsData(dataName);
    if (!data.length) {
        $(el).replaceWith('');
        return;
    }

    const thead = table.appendChild(document.createElement('thead'));
    let tr = thead.appendChild(document.createElement('tr'));
    let th;
    let i;
    for (i = 0; i < viewCols.length; i++) {
        th = tr.appendChild(document.createElement('th'));
        th.innerText = getTranslation(cols[i]);
        if (viewCols[i] === countCol) {
            th.className = 'text-right';
        }
    }

    const tbody = table.appendChild(document.createElement('tbody'));
    let td;
    let a;
    for (let j = 0; j < data.length; j++) {
        tr = tbody.appendChild(document.createElement('tr'));
        for (i = 0; i < viewCols.length; i++) {
            td = tr.appendChild(document.createElement('td'));
            if (viewCols[i] === countCol) {
                td.className = 'text-right';
            }
            if (url && viewCols[i] === dataCol) {
                a = document.createElement('a');
                a[allowHTML ? 'innerHTML' : 'innerText'] = data[j][viewCols[i]];
                a.href = url + encodeURIComponent(data[j][1]);
                td.appendChild(a);
            } else {
                td[allowHTML ? 'innerHTML' : 'innerText'] = data[j][viewCols[i]];
            }
        }
    }

    el.addEventListener('scroll', function floatingTableHead() {
        const scrolling = el.scrollTop > 0;
        el.classList.toggle('scrolling', scrolling);
        el.querySelectorAll('th').forEach(function floatTh(the) {
            the.style.transform = scrolling ? ('translateY(' + el.scrollTop + 'px)') : ''; // eslint-disable-line no-param-reassign
        });
    });
}

function getDrawTable(side) {
    return drawCountTable.bind(null,
        ['tables_' + side, 'count'], 0, 2, 'loginCountPer' + side, false,
        getStatisticsData('module_url_base') + 'detail.php?side=' + side + '&id=');
}

function getDrawCountTable(side) {
    return drawCountTable.bind(null, ['tables_' + side, 'count'], 0, 2, 'accessCounts', true, null);
}

function getterLoadCallback(getEl, callback) {
    callback(getEl);
}

function classLoadCallback(className, callback) {
    getterLoadCallback(function () { return $('.' + className + ':visible')[0]; }, callback); // eslint-disable-line func-names
}

function idLoadCallback(id, callback) {
    getterLoadCallback(document.getElementById.bind(document, id), callback);
}

function chartInit() {
    idLoadCallback('loginsDashboard', drawLoginsChart);
    ['IDP', 'SP'].forEach(function callbacksForSide(side) {
        classLoadCallback('chart-' + side + 'Chart', getDrawChart(side));
        idLoadCallback(side + 'Table', getDrawTable(side));
        idLoadCallback('detail' + side + 'Chart', drawPieChart.bind(null, 'accessCounts', [0, 2], null));
        idLoadCallback('detail' + side + 'Table', getDrawCountTable(side));
    });

    $('#dateSelector input[name=lastDays]').on('click', function submitForm() {
        this.form.submit();
    });
}

$(document).ready(function docReady() {
    const loginsDashboard = document.getElementById('loginsDashboard');
    if (loginsDashboard !== null && loginsDashboard.dataset.locale) {
        moment.locale(loginsDashboard.dataset.locale);
    }

    $('#tabdiv').tabs({
        selected: $('#tabdiv').data('activetab'),
        load: chartInit
    });
    chartInit();
});
