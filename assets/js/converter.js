(function($) {

    let converterEl = $('#currency-converter');
    if(converterEl.length === 0){
        return;
    }

    let fromEl = converterEl.find('select[name="from"]');
    let toEl = converterEl.find('select[name="to"]');
    let submitBtn = $(converterEl).find('#converter-btn');
    let resultEl = $(converterEl).find('.converter-result');
    let operationsEl = $(converterEl).find('#operations');


    let is_valid_response = function(response){
        if(typeof(response) === 'object' && response['success'] === true && response['data'] !== undefined){
            return true;
        }
        return false;
    };

    let createResultMessage = function(converted_amount){

        let from_name = fromEl.find('option[value="' + fromEl.val() + '"]').text();
        let to_name = toEl.find('option[value="' + toEl.val() + '"]').text();
        let amount = amountEl.val();

        return `${amount} ${from_name} = ${converted_amount} ${to_name}`;
    };

    let updateOperations = function(operations){
        let html = "<div class='six columns'><ul>";
        let count = 0;
        let nextCol = 5;

        for(let i = 0, len = operations.length; i < len; i++){
            if( count === nextCol ){
                html = html + "</ul></div><div class='six columns'><ul>";
                count = 0;
            }

            html = html +"<li>" + operations[i]['from']['amount'] + ' '+ operations[i]['from']['name'];
            html = html +" = ";
            html = html +operations[i]['to']['amount'] + ' '+ operations[i]['to']['name'] + "</li>";
            count++;
        }
        html = html + '</ul></div>';

        operationsEl.html(html);

    };

    let reloadOperationsData = function(){
        $.post(url, {action: 'loadLastOperations' }, function (response) {
            if(is_valid_response(response) === false){
                return;
            }
            updateOperations(response['data']);
        });
    }



    $.each([fromEl, toEl], function () {
        this.change(function () {
            submitBtn.click();
        });
    });

    let url = converterEl.attr('ajaxurl')
    let amountEl = converterEl.find('input[name="amount"]');

    submitBtn.click(function () {
        let amount = amountEl.val();
        if ( $.isNumeric(amount) === false || amount === '0'){
            return;
        }

        resultEl.text('Calculation..');

        $.post(url, {
            action: 'convert',
            from_id: $(fromEl).val(),
            to_id: $(toEl).val(),
            amount: amount,
        }, function (resp) {
            if(is_valid_response(resp) === false){
                resultEl.text('Error..');
                return;
            }
            resultEl.text(createResultMessage(resp['data']['converted_amount']));
            reloadOperationsData();

        });
    });

    reloadOperationsData();




}(jQuery));