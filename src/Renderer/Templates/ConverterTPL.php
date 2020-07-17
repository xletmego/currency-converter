<div id='currency-converter' ajaxurl='{{adminUrl}}'>
    <div class='container'>
        <h4> Currency converter</h4>
        <div class='row'>
            <div class='six columns'>
                <select name='from' class='u-full-width'>
                    {{currencyOptions}}
                </select>
            </div>
            <div class='six columns'>
                <select name='to' class='u-full-width'>
                    {{currencyOptions}}
                </select>
            </div>
        </div>

        <div class='row'>
            <div class='twelve columns text-center'>
                <h6 class='converter-result'></h6>
            </div>

        </div>


        <div class='row'>
            <div class='six columns'>
                <input class='u-full-width' name='amount' type='text' value='' placeholder='Enter Amount to Convert'/>
            </div>
            <div class='one-third column u-pull-right' >
                <button id='converter-btn' class='button button-primary u-full-width'>
                    Convert
                </button>
            </div>
        </div>
        <div id='operations' class='row'>

        </div>
    </div>
</div>
