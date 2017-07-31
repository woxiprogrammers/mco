/**
 * Created by harsha on 7/19/17.
 */

/*<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
 <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
 */
$(document).ready(function(){
    var quotationId = $("#quotation_id").val();
    var url = "/bill/product/get-descriptions/"+quotationId+"/%QUERY";
    var citiList = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: url,
            filter: function(x) {
                if($(window).width()<420){
                    $("#header").addClass("fixed");
                }
                return $.map(x, function (data) {
                    return {
                        id:data.id,
                        description:data.description
                    };
                });
            },
            wildcard: "%QUERY"
        }
    });

    $(".product-checkbox").on('click',function(){
        var elementId = $(this).parent().next().next().find('.product_description').attr('id');
        if($(this).prop('checked') == true){
            $("#"+elementId).addClass('typeahead');
            citiList.initialize();
            $("#"+elementId).typeahead(null, {
                displayKey: 'name',
                engine: Handlebars,
                source: citiList.ttAdapter(),
                limit: 30,
                templates: {
                    empty: [

                    ].join('\n'),
                    suggestion: Handlebars.compile('<div class="autosuggest"><strong>{{description}}</strong></div>')
                }
            }).on('typeahead:selected', function (obj, datum) {
                var POData = $.parseJSON(JSON.stringify(datum));
                $("#"+elementId).closest(".input-group").find('.product-description-id').attr('value',POData.id);
                $("#"+elementId).typeahead('val',POData.description);
                return false;
            }).on('typeahead:open', function (obj, datum) {

            });
        }else{
            $("#"+elementId).closest(".input-group").find('.product-description-id').removeAttr('value');
            $(".typeahead").typeahead('destroy');
        }
    });

    $(".product-checkbox:checked").each(function(){
        var elementId = $(this).parent().next().next().find('.product_description').attr('id');
        $("#"+elementId).addClass('typeahead');
        citiList.initialize();
        $("#"+elementId).typeahead(null, {
            displayKey: 'name',
            engine: Handlebars,
            source: citiList.ttAdapter(),
            limit: 30,
            templates: {
                empty: [

                ].join('\n'),
                suggestion: Handlebars.compile('<div class="autosuggest"><strong>{{description}}</strong></div>')
            }
        }).on('typeahead:selected', function (obj, datum) {
            var POData = $.parseJSON(JSON.stringify(datum));
            $("#"+elementId).closest(".input-group").find('.product-description-id').attr('value',POData.id);
            $("#"+elementId).typeahead('val',POData.description);
            return false;
        }).on('typeahead:open', function (obj, datum) {

        });
    });

});