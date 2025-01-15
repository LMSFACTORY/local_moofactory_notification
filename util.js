$('#id_customfield_courseeventscheckavailability').on('change', function(event) {
    if($('#id_customfield_courseeventscheckavailability').is(':checked')){
        $('#id_customfield_courseeventscheckdateavailability').removeAttr('disabled');
        $('#id_customfield_courseeventscheckgroupavailability').removeAttr('disabled');
    }
    else{
        $('#id_customfield_courseeventscheckdateavailability').attr('disabled', 'disabled');
        $('#id_customfield_courseeventscheckgroupavailability').attr('disabled', 'disabled');
    }
});

function initCheckAvailability(state){
    if(!state){
        $('#id_customfield_courseeventscheckdateavailability').attr('disabled', 'disabled');
        $('#id_customfield_courseeventscheckgroupavailability').attr('disabled', 'disabled');
    }
}

function initModuleCheckAvailability(state, dateElement, groupElement){
    if(!state){
        $(dateElement).attr('disabled', 'disabled');
        $(groupElement).attr('disabled', 'disabled');
    }
}

// Champs de fusion.
const allChampsFusion = document.querySelectorAll(
    "table.merge-fields td:first-child"
);

allChampsFusion.forEach((champsFusion) => {
    champsFusion.style.cursor = "pointer";
    champsFusion.addEventListener("click", (e) => {
        $(".copyAlert").remove();
        const textTocopy = champsFusion.innerHTML;
        const alertContainer = document.createElement("span");
        alertContainer.append(M.str.local_moofactory_notification.copied);
        alertContainer.classList.add("copyAlert");

        $(champsFusion).append($(alertContainer));
        $(alertContainer).fadeIn(200);
        
        setTimeout(() => {
            $(alertContainer).fadeOut(200, function() {
                $(this).remove();
            });
        }, 1000);
        navigator.clipboard.writeText(textTocopy);
    });
});

const style = document.createElement("style");
document.head.appendChild(style);
style.sheet.insertRule(`
    .copyAlert {
        display: none;
        color: #fff;
        background: #4BB543;
        border-radius: 5px;
        margin-left: 0.3em;
        padding:  2px 5px;
        position: relative;
    }
`);