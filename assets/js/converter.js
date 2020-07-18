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
    let switchBtn = $(converterEl).find('#switch');
    let url = converterEl.attr('ajaxurl')
    let amountEl = converterEl.find('input[name="amount"]');


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

            html = html +"<li class='operation-row' amount='" + operations[i]['from']['amount'] + "' ";
            html = html +"from-id='" + operations[i]['from']['id'] + "' to-id='" + operations[i]['to']['id'] + "'>";
            html = html + operations[i]['from']['amount'] + ' '+ operations[i]['from']['symbol'];
            html = html +" = ";
            html = html +operations[i]['to']['amount'] + ' '+ operations[i]['to']['symbol'] + "</li>";
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

    let uniqueSelectOptions = function(firstRun){

        let targetName = 'from';

        if($(this).attr('name') === 'from'){
            targetName = 'to';

        }
        let val = $(this).val();
        let targetEl = $(converterEl).find('select[name="' + targetName + '"]');
        $(targetEl).find('option.hidden').removeClass('hidden');
        $(targetEl).find('option[value="' + val + '"]').addClass('hidden');

        if(firstRun !== undefined){
            let newVal = $(targetEl).find('option:not(.hidden)').first().attr('value');
            $(targetEl).val(newVal);
        }
    }



    $.each([fromEl, toEl], function () {
        this.change(function () {
            uniqueSelectOptions.apply(this);
            submitBtn.click();
        });
    });

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

    switchBtn.click(function () {
        let left = $(fromEl).val();
        let right = $(toEl).val();
        $(fromEl).val(right);
        $(toEl).val(left).change();
    });

    operationsEl.click(function (e) {
        let rowEl = $(e.target);
        if(rowEl.hasClass('operation-row') === false){
            return;
        }


        $(fromEl).val(rowEl.attr('from-id'));
        $(toEl).val(rowEl.attr('to-id'));
        $(amountEl).val(rowEl.attr('amount'));

        fromEl.change();

    });

    uniqueSelectOptions.apply(fromEl, [true]);

    reloadOperationsData();




}(jQuery));