function saveFormData($, data, moduleId){
    if(marathonRegDebug) console.log(moduleId);
    if(marathonRegDebug) console.log(data);

    $('#waiter_'+moduleId).fadeIn('slow', function(){
        $.when(ajaxCall(data,'saveRegistration')).then(function(response){
            if(response.success){
                if(marathonRegDebug) console.log(response);
                if(response.data.success){
                    $('div.nx-registration_'+moduleId+' .data-saved table.info').html(response.data.html);
                    //PaymentInfo:
                    $('div.nx-registration_'+moduleId+' .referencenum').html(response.data.data.reference_num);
                    $('div.nx-registration_'+moduleId+' .price').html(response.data.data.total_price);


                    //Print
                    let $toPrint = $('div.nx-registration_'+moduleId+' div.registrationInfo').clone();

                    // Send Mail to user which created the item:
                    registrationData = response.data.data;
                    sendMail($,[response.data.data.usermail], registrationData);

                    $(document.body).append($toPrint);

                    $('div.nx-registration_'+moduleId+' div.registration-container').toggleClass('uk-hidden');
                    $('div.nx-registration_'+moduleId).find('div.nx-marathon-registration-done').toggleClass('uk-hidden');

                    var $scrolling = UIkit.scroll();
                    $scrolling.scrollTo($('body'));

                    setTimeout(function (){
                        $('#waiter_'+moduleId).fadeOut();
                    },2000);
                }else{
                    // Some error occured
                    if(marathonRegDebug) console.log(response.data.txt);
                    $('#waiter_'+moduleId).html(response.data.html);
                }

            }else{
                // Some error occured
                if(marathonRegDebug) console.log(response.data.txt);
                $('#waiter_'+moduleId).html(response.data.html);
            }
        },function(error){
            if(error.hasOwnProperty('data')){
                if(error.data.hasOwnProperty('errormsg')){
                    if(marathonRegDebug) console.log(error.data.errormsg);
                }else{
                    if(marathonRegDebug) console.log(error.data);
                }
            }else{
                if(marathonRegDebug) console.log(error);
            }


        });
    });

}

function ajaxCall(data, method){
    let request = {
        'option': 'com_ajax',
        'module': 'nxmarathon_reg',
        'method': method,
        'data': JSON.stringify(data),
        'format': 'json'
    };
    return jQuery.ajax({
        type: 'POST',
        data: request,
    });
}

function sendMail($, adresses , contents, toRunners = false){
    contents.toRunners = toRunners;
    if(marathonRegDebug) console.log('sendmail called');
    if(adresses.length){
        if(marathonRegDebug) console.log(adresses);
        let data = {};
        data.subject = 'Registration eingegangen';
        data.contents = contents;
        data.recipient = adresses;
        if(marathonRegDebug) console.log(data);
        $.when(ajaxCall(data,'sendMail')).then(function(response){
            if(marathonRegDebug) console.log(response);
        },function (error){
            if(marathonRegDebug) console.log(error);
        });
    }else{
        if(marathonRegDebug) console.log('Keine Adressen mitgeteilt');
    }

}