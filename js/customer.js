$(document).ready(function() {

    // STATUS получаем ajax-ом при первой загрузке страницы
    // так можно динамически менять количесто и названия статусов
    // для теста задам STATUS константой
    var STATUS = {
        '1':'новый',
        '2':'зарегистрирован',
        '3':'отказался',
        '4':'недоступен'
    };

    $('#date_create').datepicker({
        language: "ru",
        format: 'dd.mm.yyyy'
    });

    //заполнить таблицу полученными записями
    function get_all() {
        var data = {
            'action': 'get_all'
        };

        $.ajax({
            url: 'customer.php',
            dataType: "json",
            type: 'POST',
            data: data,
            success: function (data, textStatus, jqXHR) {
                var tr = '';
                $.each(data.rows, function(i,v) {
                    options = '';
                        for(var ps in STATUS) {
                            var selected = (ps == v.status ? 'selected' : '');
                            options += '<option value="'+ps+'" '+selected+' >'+STATUS[ps]+'</option>';
                        }

                    str_select =
                        '<select cid="'+i+'" class="xc_edit_status">'+ options +'</select>';

                    tr +=
                    '<tr>' +
                        '<td>'+ (i) +'</td>'+
                        '<td class="fio">'+ v.fio +'</td>'+
                        '<td class="phone">'+ v.phone +'</td>'+
                        '<td class="date_create">'+ v.date_create +'</td>'+
                        '<td class="status">'+ str_select +'</td>'+
                    '</tr>';
                });

                $("#table_customers tbody").html(tr);

                return data;
            }
        });
    }

    get_all();

    //добавить запись
    $('#add_customer').click(function(){

        $('#fio').val('');
        $('#phone').val('');
        $('#status').val('');
        $('#date_create').val('');

        $('#customer_edit').modal();
    });

    // создаём или изменяем запись
    $('body').on("click", "#save_customers", function(){

        var data = {
            'action': 'add',
            'fio': $('#fio').val(),
            'phone': $('#phone').val(),
            'status': $('#status').val(),
            'date_create': $('#date_create').val()
        };

        $.ajax({
            url: 'customer.php',
            dataType: "json",
            type: 'POST',
            data: data,
            success: function (data, textStatus, jqXHR) {
                if(data.execute) {
                    alert('Запись добавлена');
                    get_all();
                }
            }
        });

        $('#customer_edit').modal('hide');
    });


    // редактируем статус
    $('body').on("change", ".xc_edit_status", function(){

        var data = {
            'action': 'edit_status',
            'cid': $(this).attr('cid'),
            'status': $(this).val()
        };

        $.ajax({
            url: 'customer.php',
            dataType: "json",
            type: 'POST',
            data: data,
            success: function (data, textStatus, jqXHR) {
                if(data.execute){
                    alert('Статус изменён!');
                }else{
                    alert('Статус НЕ изменён!');
                }
            }
        });

    });

    //обработка вкладок
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var el = $(e.target);
        switch(el.attr('href')){
            case '#home':
                get_all();
                break;
            case '#report':
                break;
        }
    });

    // показ конверсии
    $('body').on("click", "#show_conversion", function(){
        var data = {
            'action': 'report',
            'number_days': $("#number_days").val()
        };

        $.ajax({
            url: 'customer.php',
            dataType: "json",
            type: 'POST',
            data: data,
            success: function (data, textStatus, jqXHR) {
                if(data.execute){
                    show_chart(data);
                }else{
                    alert('Error chart data');
                }

            }
        });
    });

    // отображаем график
    function show_chart(data){

        var rows = data.rows;

        var s1 = [];
        var ticks = [];

        // количество периодов
        var cnt_priod = data.end_key;
        for(i=0; i <= cnt_priod; i++ ){
            ticks.push(i);
            if(rows[i])
                s1.push(rows[i].conversion*1);
            else
                s1.push(0);
        }

        $("#chartdiv").html('');

        $.jqplot.config.enablePlugins = true;
        plot1 = $.jqplot('chartdiv', [s1], {
            // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
            animate: !$.jqplot.use_excanvas,
            seriesDefaults:{
                renderer:$.jqplot.BarRenderer,
                pointLabels: { show: true }
            },
            axes: {
                xaxis: {
                    renderer: $.jqplot.CategoryAxisRenderer,
                    ticks: ticks
                }
            },
            highlighter: { show: false }
        });
    }

});