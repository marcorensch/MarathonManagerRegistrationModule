let maxRunners = 5;
let families = true;
let registrationData;
let emailPattern = "/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";

jQuery('document').ready(function($){
    //addRunnerFields(2);
    addRunnerNum();

    /* Remove / Add Family */
    $('select#parcours').on('change',function() {
        switch ($(this).val()) {
            case '1':
                families = false;
                break;
            default:
                families = true;
        }
        if(families){
            if($("select#category option[value='4']").length === 0){
                $('select#category').append('<option value="4">Family (F)</option>');
            }
        }else{
            if($("select#category").val() == '4'){
                $("select#category").val('');
            }
            $("select#category option[value='4']").remove();

        }

    });

    /* Update MaxRunners based on category */
    $('select#category').on('change',function(){
        switch($(this).val()){
            case '1':
            case '2':
            case '3':
                maxRunners = 2;
                break;
            case '4':
            default:
                maxRunners = 5;
        }
        // remove runners over the limit
        checkRunnersCount();
    });

    /* Add Runner */
    $('.addRunner').on('click',function(){
        let actualRunners = $('.repeatable_container > div').length;
        if(actualRunners < maxRunners) {
            addRunnerFields();
            addRunnerNum();
        }else{
            UIkit.notification({
                message: '<b>Hinweis:</b><br>Maximale Anzahl Läufer für die gewählte Kategorie erreicht.',
                status: 'primary',
                pos: 'top-center',
                timeout: 5000
            });
        }
    });

    /* Remove Runner */
    $('div#runners_container').on('click', '.remove_runner',function(){
        let runnerItem = $(this).parents('.repeatable_element');
        if(marathonRegDebug) console.log(runnerItem);
        removeSelectedRunner(runnerItem);
    });

    /* The Functions */
    function addRunnerNum(){
        $('.repeatable_container > div').each(function(i){
            let runnerNum = i+1;
            $(this).find('.runner_num').text(runnerNum);
            //update all name fields
            $(this).find('input, select').each(function (){
                let oldName = $(this).attr('name'),
                    newName = oldName.replace(new RegExp("_(\\d?)_"),'_'+runnerNum+'_');
                $(this).attr('name', newName);
            });
        });
    }

    function checkForRunnerEmailFields(){
        $('.repeatable_container > div').each(function(i){
            if(i < 2 && $(this).find('div.runner_email_container').length === 0){
                if(marathonRegDebug) console.log('We have to add the email fields back for '+i);
                addRunnerMailFields($(this));
            }else{
                // All good here its runner 3,4,n or E-Mail fields are there
            }
        });
    }

    function addRunnerFields(){
        let runnerData = $('.clonesrc').find($('.repeatable_element')).clone();
        // Remove E-Mail Fields from template before append for runner 3 ... n
        if($('.repeatable_element > div').length > 2){
            removeEmailFromTemplate(runnerData);
        }
        runnerData.appendTo($('.repeatable_container'));
        // Change Grid Classes if more then 2 runners
        changeGridCls();
    }

    function addRunnerMailFields(runnerItem){
        let emailTemplate = $('.runner_email_insertion > div').clone();
        if(marathonRegDebug) console.log(emailTemplate);
        let target = runnerItem.find('div.runner_email_outter');
        if(marathonRegDebug) console.log(target);
        emailTemplate.appendTo($('div.runner_email_outter'));
        if(marathonRegDebug) console.log(target);
    }


    function removeSelectedRunner(item){
        UIkit.modal.confirm('Soll der Läufer wirklich gelöscht werden?').then(function() {
            if(marathonRegDebug) console.log('Confirmed.');
            $.when(item.remove()).then(function(){
                // Update Numbers
                addRunnerNum();
                changeGridCls();
                // checkForRunnerEmailFields();
            });
        }, function () {
            if(marathonRegDebug) console.log('Rejected.');
        });

    }

    function changeGridCls(){
        let actualRunners = $('.repeatable_container > div').length;
        if(actualRunners > 2){
            $('.repeatable_container').removeClass('uk-child-width-expand@m');
            $('.repeatable_container').addClass('uk-child-width-1-2@m');
        }else{
            $('.repeatable_container').addClass('uk-child-width-expand@m');
            $('.repeatable_container').removeClass('uk-child-width-1-2@m');
        }
    }

    function removeEmailFromTemplate(element){
        element.find('.runner_email_container').remove();
    }

    function checkRunnersCount(){
        if($('.repeatable_container > div').length > maxRunners){
            let runners = $('.repeatable_container > div');
            let index = maxRunners;
            for(index; index < runners.length; index++){
                runners[index].remove();
                let runnerNum = index+1;
                UIkit.notification({
                    message: '<b>Achtung</b><br>Läufer '+runnerNum+' entfernt, Das Limit für die gewählte Kategorie beträgt '+maxRunners+' Läufer.',
                    status: 'warning',
                    pos: 'top-center',
                    timeout: 10000
                });
            }
        }
    }

    $('body').on('click','button.sendmail',function(){
        let moduleId = $(this).data('module-id');
        let recipients = [];
        $('div.nx-registration_'+moduleId+' div.nx-marathon-registration-done span.runner-address').each(function(){
            recipients.push($(this).text());
        });
        if(marathonRegDebug) console.log(recipients);

        sendMail($, recipients, registrationData, true);
        UIkit.notification({
            message: 'Der Mailversand wurde ausgelöst.',
            status: 'primary',
            pos: 'bottom-right',
            timeout: 5000
        });
    });

});

jQuery(document).ready(function($){
    $( ".registration-container input, .registration-container select" )
        .focusout(function() {
            $(this).css('border-width','1px');
            $(this).css('border-color','#ebebed');
        });
});

// Administrator stuff
jQuery(document).ready(function($){
    $(document).on('click','button.reg-back', function(){
        let moduleId = $(this).data('module-id');
        $('div.nx-registration_'+moduleId+' div.registration-container').toggleClass('uk-hidden');
        $('div.nx-registration_'+moduleId).find('div.nx-marathon-registration-done').toggleClass('uk-hidden');
    });

    $(document).on('click','.demofill', function(){
        console.log('clicked');
        let index = 0;
        let $form = $(this).parents('form');
        let strings = ['foo','bar','testing','test','tester'];
        let emails = ['demo@test.ch','some@nx-designs.ch','foo@bar.it','run@email.ch'];

        $form.find("input[type='text']:not(input[name='eventid'], input[name='lastinfomailing'], input[name='eventyear'], input[name='maps_price_total'])").each(function(){
            index = getRandomArbitrary(0,strings.length-1);
            console.log(index);
            console.log(strings[index]);
            $(this).val(strings[index]);
        });

        $form.find("input[type='email']").each(function(){
            index = getRandomArbitrary(0,emails.length-1);
            console.log(index);
            console.log(emails[index]);
            $(this).val(emails[index]);
        });
        $form.find("input[type='number']").each(function(){
            let value = $(this).attr('min');
            $(this).val(value);
        });
        $form.find("input[type='checkbox']:not('input.runner_newsletter')").each(function(){
            $(this).attr('checked',true);
        });
        $form.find("input[type='tel']").each(function(){
            $(this).val('+41790000000');
        });
        $form.find("select:not(select[name='user_id'])").each((i, el) => {
            let $options = $(el).find('option');
            let index = getRandomArbitrary(1, $options.length);
            $options.eq(index).prop('selected', true);
        });
        $form.find("select[name='user_id']").val('0');
    });

    function getRandomArbitrary(min, max) {
        return Math.floor(Math.random() * (max - min) + min);
    }
});